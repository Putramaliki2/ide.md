(function (h, domain) {
  "use strict";

  const report = { id: "report-1", userId: "owner-1", type: "lost", status: "open" };

  h.test("pemilik dapat menghapus laporan miliknya", () => {
    h.truthy(domain.canDeleteReport(report, { id: "owner-1" }));
  });

  h.test("pengguna lain tidak dapat menghapus laporan", () => {
    h.falsy(domain.canDeleteReport(report, { id: "user-2" }));
  });

  h.test("pengunjung tanpa login tidak dapat menghapus laporan", () => {
    h.falsy(domain.canDeleteReport(report, null));
  });

  h.test("status terbuka mempertahankan jenis laporan", () => {
    h.equal(domain.reportState(report), "lost");
    h.equal(domain.reportState({ ...report, type: "found" }), "found");
  });

  h.test("status selesai dipetakan menjadi Sudah kembali", () => {
    h.equal(domain.reportState({ ...report, status: "done" }), "returned");
  });
})(TestHarness, LostFoundDomain);
