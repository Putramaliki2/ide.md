create table if not exists public.profiles (
  user_id uuid primary key references auth.users(id) on delete cascade,
  role text not null default 'user' check (role in ('user', 'admin')),
  created_at timestamptz not null default now()
);

create or replace function public.is_admin()
returns boolean
language sql
stable
security definer
set search_path = public
as $$
  select exists (
    select 1 from public.profiles
    where user_id = auth.uid() and role = 'admin'
  );
$$;

revoke all on function public.is_admin() from public;
grant execute on function public.is_admin() to authenticated;

create or replace function public.create_profile_for_user()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin
  insert into public.profiles (user_id) values (new.id)
  on conflict (user_id) do nothing;
  return new;
end;
$$;

drop trigger if exists on_auth_user_created on auth.users;
create trigger on_auth_user_created
  after insert on auth.users
  for each row execute procedure public.create_profile_for_user();

insert into public.profiles (user_id)
select id from auth.users
on conflict (user_id) do nothing;

alter table public.profiles enable row level security;
drop policy if exists "Users can read their own profile" on public.profiles;
create policy "Users can read their own profile"
  on public.profiles for select to authenticated
  using (user_id = auth.uid() or public.is_admin());

create table if not exists public.lostfound_items (
  id uuid primary key default gen_random_uuid(),
  user_id uuid not null references auth.users(id) on delete cascade,
  type text not null check (type in ('lost', 'found')),
  name text not null check (char_length(btrim(name)) between 1 and 80),
  category text not null check (char_length(btrim(category)) between 1 and 40),
  location text not null check (char_length(btrim(location)) between 1 and 100),
  date date not null,
  description text not null default '' check (char_length(description) <= 300),
  contact text not null check (char_length(btrim(contact)) between 1 and 80),
  photo text,
  status text not null default 'open' check (status in ('open', 'done')),
  review_status text,
  created_at timestamptz not null default now()
);

alter table public.lostfound_items
  add column if not exists review_status text;
update public.lostfound_items set review_status = 'verified' where review_status is null;
alter table public.lostfound_items alter column review_status set default 'pending';
alter table public.lostfound_items alter column review_status set not null;
alter table public.lostfound_items drop constraint if exists lostfound_items_review_status_check;
alter table public.lostfound_items
  add constraint lostfound_items_review_status_check
  check (review_status in ('pending', 'verified', 'rejected'));
alter table public.lostfound_items replica identity full;
alter table public.lostfound_items enable row level security;

drop policy if exists "Authenticated users can read reports" on public.lostfound_items;
drop policy if exists "Users can create their own reports" on public.lostfound_items;
drop policy if exists "Users can claim open reports they do not own" on public.lostfound_items;
drop policy if exists "Authenticated users can update claim details" on public.lostfound_items;
drop policy if exists "Users can delete their own reports" on public.lostfound_items;
drop policy if exists "Owners and admins can read reports" on public.lostfound_items;
create policy "Owners and admins can read reports"
  on public.lostfound_items for select to authenticated
  using (user_id = auth.uid() or public.is_admin());

drop policy if exists "Users can create pending reports" on public.lostfound_items;
create policy "Users can create pending reports"
  on public.lostfound_items for insert to authenticated
  with check (auth.uid() = user_id and review_status = 'pending' and status = 'open');

drop policy if exists "Admins can moderate reports" on public.lostfound_items;
create policy "Admins can moderate reports"
  on public.lostfound_items for update to authenticated
  using (public.is_admin())
  with check (public.is_admin());

drop policy if exists "Owners and admins can delete reports" on public.lostfound_items;
create policy "Owners and admins can delete reports"
  on public.lostfound_items for delete to authenticated
  using (user_id = auth.uid() or public.is_admin());

create table if not exists public.lostfound_claims (
  id uuid primary key default gen_random_uuid(),
  item_id uuid not null references public.lostfound_items(id) on delete cascade,
  claimant_id uuid not null references auth.users(id) on delete cascade,
  name text not null check (char_length(btrim(name)) between 1 and 60),
  contact text not null check (char_length(btrim(contact)) between 1 and 80),
  status text not null default 'pending' check (status in ('pending', 'approved', 'rejected')),
  created_at timestamptz not null default now()
);

create unique index if not exists lostfound_one_pending_claim_per_item
  on public.lostfound_claims (item_id) where status = 'pending';
alter table public.lostfound_claims enable row level security;

create or replace function public.can_claim_item(p_item_id uuid)
returns boolean
language sql
stable
security definer
set search_path = public
as $$
  select exists (
    select 1 from public.lostfound_items
    where id = p_item_id
      and user_id <> auth.uid()
      and review_status = 'verified'
      and status = 'open'
  );
$$;

revoke all on function public.can_claim_item(uuid) from public;
grant execute on function public.can_claim_item(uuid) to authenticated;

drop policy if exists "Claimants owners and admins can read claims" on public.lostfound_claims;
create policy "Claimants owners and admins can read claims"
  on public.lostfound_claims for select to authenticated
  using (
    claimant_id = auth.uid()
    or public.is_admin()
    or exists (
      select 1 from public.lostfound_items i
      where i.id = item_id and i.user_id = auth.uid()
    )
  );

drop policy if exists "Users can submit claims for verified reports" on public.lostfound_claims;
create policy "Users can submit claims for verified reports"
  on public.lostfound_claims for insert to authenticated
  with check (
    claimant_id = auth.uid()
    and status = 'pending'
    and public.can_claim_item(item_id)
  );

create or replace function public.review_report(p_report_id uuid, p_decision text)
returns void
language plpgsql
security definer
set search_path = public
as $$
begin
  if not public.is_admin() then
    raise exception 'Admin access required';
  end if;
  if p_decision is null or p_decision not in ('verified', 'rejected') then
    raise exception 'Invalid report decision';
  end if;

  update public.lostfound_items
    set review_status = p_decision
    where id = p_report_id;
  if not found then
    raise exception 'Report does not exist';
  end if;

  if p_decision = 'rejected' then
    update public.lostfound_claims
      set status = 'rejected'
      where item_id = p_report_id and status = 'pending';
  end if;
end;
$$;

revoke all on function public.review_report(uuid, text) from public;
grant execute on function public.review_report(uuid, text) to authenticated;

create or replace function public.review_claim(p_claim_id uuid, p_decision text)
returns void
language plpgsql
security definer
set search_path = public
as $$
declare
  claim_row public.lostfound_claims%rowtype;
begin
  if not public.is_admin() then
    raise exception 'Admin access required';
  end if;
  if p_decision is null or p_decision not in ('approved', 'rejected') then
    raise exception 'Invalid claim decision';
  end if;

  select * into claim_row
  from public.lostfound_claims
  where id = p_claim_id
  for update;
  if not found or claim_row.status <> 'pending' then
    raise exception 'Claim is no longer pending';
  end if;

  if p_decision = 'approved' then
    update public.lostfound_items
      set status = 'done'
      where id = claim_row.item_id
        and status = 'open'
        and review_status = 'verified';
    if not found then
      raise exception 'Report is no longer available';
    end if;
  end if;

  update public.lostfound_claims
    set status = p_decision
    where id = p_claim_id;
end;
$$;

revoke all on function public.review_claim(uuid, text) from public;
grant execute on function public.review_claim(uuid, text) to authenticated;

create or replace view public.lostfound_public as
  select id, type, name, category, location, date, description, status, created_at, photo
  from public.lostfound_items
  where review_status = 'verified';

revoke all on public.lostfound_items from anon, authenticated;
grant select, insert, delete on public.lostfound_items to authenticated;
grant update on public.lostfound_items to authenticated;
revoke all on public.lostfound_claims from anon, authenticated;
grant select, insert on public.lostfound_claims to authenticated;
revoke all on public.profiles from anon, authenticated;
grant select on public.profiles to authenticated;
revoke all on public.lostfound_public from public, anon, authenticated;
grant select on public.lostfound_public to anon, authenticated;

do $$
begin
  if not exists (
    select 1 from pg_publication_tables
    where pubname = 'supabase_realtime'
      and schemaname = 'public'
      and tablename = 'lostfound_items'
  ) then
    alter publication supabase_realtime add table public.lostfound_items;
  end if;
  if not exists (
    select 1 from pg_publication_tables
    where pubname = 'supabase_realtime'
      and schemaname = 'public'
      and tablename = 'lostfound_claims'
  ) then
    alter publication supabase_realtime add table public.lostfound_claims;
  end if;
end
$$;
