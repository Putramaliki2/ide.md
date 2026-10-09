(function (h, domain) {
  "use strict";

  const report = { id: "item-1", userId: "owner-1", type: "found", status: "open" };

  h.test("pengunjung tanpa login tidak bisa mengajukan klaim", () => {
    h.falsy(domain.canClaim(report, null));
  });

  h.test("pengguna lain dapat mengklaim laporan terbuka", () => {
    h.truthy(domain.canClaim(report, { id: "user-2" }));
  });

  h.test("pemilik laporan tidak bisa mengklaim laporannya sendiri", () => {
    h.falsy(domain.canClaim(report, { id: "owner-1" }));
  });

  h.test("laporan yang sudah selesai tidak bisa diklaim ulang", () => {
    h.falsy(domain.canClaim({ ...report, status: "done" }, { id: "user-2" }));
  });

  h.test("laporan hilang terbuka juga dapat diklaim sebagai barang yang ditemukan", () => {
    h.truthy(domain.canClaim({ ...report, type: "lost" }, { id: "finder-1" }));
  });
})(TestHarness, LostFoundDomain);
