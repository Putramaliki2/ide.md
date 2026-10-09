create table if not exists public.lostfound_items (
  id uuid primary key default gen_random_uuid(),
  user_id uuid not null references auth.users(id) on delete cascade,
  type text not null check (type in ('lost', 'found')),
  name text not null check (char_length(name) between 1 and 80),
  category text not null check (char_length(category) between 1 and 40),
  location text not null check (char_length(location) between 1 and 100),
  date date not null,
  description text not null default '' check (char_length(description) <= 300),
  contact text not null check (char_length(contact) between 1 and 80),
  photo text,
  status text not null default 'open' check (status in ('open', 'done')),
  who text,
  who_contact text,
  created_at timestamptz not null default now()
);

alter table public.lostfound_items enable row level security;
alter table public.lostfound_items replica identity full;

drop policy if exists "Authenticated users can read reports" on public.lostfound_items;
create policy "Authenticated users can read reports"
  on public.lostfound_items for select to authenticated using (true);

drop policy if exists "Users can create their own reports" on public.lostfound_items;
create policy "Users can create their own reports"
  on public.lostfound_items for insert to authenticated
  with check (auth.uid() = user_id);

drop policy if exists "Authenticated users can update claim details" on public.lostfound_items;
drop policy if exists "Users can claim open reports they do not own" on public.lostfound_items;
create policy "Users can claim open reports they do not own"
  on public.lostfound_items for update to authenticated
  using (status = 'open' and auth.uid() <> user_id)
  with check (
    status = 'done'
    and auth.uid() <> user_id
    and who is not null
    and char_length(btrim(who)) between 1 and 60
    and who_contact is not null
    and char_length(btrim(who_contact)) between 1 and 80
  );

drop policy if exists "Users can delete their own reports" on public.lostfound_items;
create policy "Users can delete their own reports"
  on public.lostfound_items for delete to authenticated using (auth.uid() = user_id);

create or replace view public.lostfound_public as
  select id, type, name, category, location, date, description, status, created_at
  from public.lostfound_items;

revoke all on public.lostfound_items from anon, authenticated;
grant select, insert, delete on public.lostfound_items to authenticated;
grant update (status, who, who_contact) on public.lostfound_items to authenticated;
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
end
$$;
