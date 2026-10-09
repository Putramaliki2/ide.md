(function (h, domain) {
  "use strict";

  const validReport = {
    type: "lost",
    name: "Dompet",
    category: "Dompet dan tas",
    location: "Kantin",
    date: "2026-10-09",
    description: "",
    contact: "08123456789"
  };

  h.test("laporan valid diterima dan foto boleh kosong", () => {
    h.equal(domain.validateReport(validReport), null);
  });

  h.test("jenis laporan selain hilang atau temuan ditolak", () => {
    h.equal(domain.validateReport({ ...validReport, type: "unknown" }), "invalid_type");
  });

  h.test("nama wajib diisi dan maksimal 80 karakter", () => {
    h.equal(domain.validateReport({ ...validReport, name: " " }), "name_required");
    h.equal(domain.validateReport({ ...validReport, name: "x".repeat(81) }), "name_too_long");
    h.equal(domain.validateReport({ ...validReport, name: "x".repeat(80) }), null);
  });

  h.test("lokasi dan kontak wajib diisi", () => {
    h.equal(domain.validateReport({ ...validReport, location: "" }), "location_required");
    h.equal(domain.validateReport({ ...validReport, contact: " " }), "contact_required");
  });

  h.test("tanggal kalender tidak valid ditolak", () => {
    h.equal(domain.validateReport({ ...validReport, date: "2026-02-30" }), "invalid_date");
  });

  h.test("deskripsi dibatasi 300 karakter", () => {
    h.equal(domain.validateReport({ ...validReport, description: "x".repeat(300) }), null);
    h.equal(domain.validateReport({ ...validReport, description: "x".repeat(301) }), "description_too_long");
  });
})(TestHarness, LostFoundDomain);
