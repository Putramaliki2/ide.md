(function (h, domain) {
  "use strict";

  const reports = [
    { id: "1", type: "lost", status: "open", name: "Dompet Cokelat", category: "Dompet dan tas", location: "Kantin", description: "" },
    { id: "2", type: "found", status: "open", name: "Kunci Motor", category: "Kunci", location: "Parkiran", description: "Gantungan merah" },
    { id: "3", type: "found", status: "done", name: "Earphone", category: "Elektronik", location: "Perpustakaan", description: "" }
  ];

  h.test("filter Semua mengembalikan seluruh laporan", () => {
    h.deepEqual(domain.filterReports(reports, "all", "").map((item) => item.id), ["1", "2", "3"]);
  });

  h.test("filter Hilang dan Temuan menggunakan jenis laporan", () => {
    h.deepEqual(domain.filterReports(reports, "lost", "").map((item) => item.id), ["1"]);
    h.deepEqual(domain.filterReports(reports, "found", "").map((item) => item.id), ["2"]);
  });

  h.test("laporan selesai masuk ke filter Sudah kembali", () => {
    h.deepEqual(domain.filterReports(reports, "returned", "").map((item) => item.id), ["3"]);
  });

  h.test("pencarian tidak peka huruf besar dan mencakup kategori, lokasi, ciri", () => {
    h.deepEqual(domain.filterReports(reports, "all", "DOMPET").map((item) => item.id), ["1"]);
    h.deepEqual(domain.filterReports(reports, "all", "parkiran").map((item) => item.id), ["2"]);
    h.deepEqual(domain.filterReports(reports, "all", "merah").map((item) => item.id), ["2"]);
  });

  h.test("pencarian dan tab diterapkan bersamaan", () => {
    h.deepEqual(domain.filterReports(reports, "found", "kunci").map((item) => item.id), ["2"]);
  });
})(TestHarness, LostFoundDomain);
