(function (h, domain) {
  "use strict";

  h.test("nama dan kontak klaim wajib diisi", () => {
    h.equal(domain.validateClaim({ name: " ", contact: "0812" }), "name_required");
    h.equal(domain.validateClaim({ name: "Pemilik", contact: "" }), "contact_required");
  });

  h.test("spasi tepi tidak menggagalkan isian valid", () => {
    h.equal(domain.validateClaim({ name: " Pemilik ", contact: " 081234 " }), null);
  });

  h.test("nama klaim dibatasi 60 karakter", () => {
    h.equal(domain.validateClaim({ name: "x".repeat(60), contact: "0812" }), null);
    h.equal(domain.validateClaim({ name: "x".repeat(61), contact: "0812" }), "name_too_long");
  });

  h.test("kontak klaim dibatasi 80 karakter", () => {
    h.equal(domain.validateClaim({ name: "Pemilik", contact: "x".repeat(80) }), null);
    h.equal(domain.validateClaim({ name: "Pemilik", contact: "x".repeat(81) }), "contact_too_long");
  });
})(TestHarness, LostFoundDomain);
