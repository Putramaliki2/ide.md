(function (root) {
  "use strict";

  const validTypes = ["lost", "found"];

  function validateReport(report) {
    if (!report || !validTypes.includes(report.type)) return "invalid_type";
    const name = String(report.name || "").trim();
    const category = String(report.category || "").trim();
    const location = String(report.location || "").trim();
    const contact = String(report.contact || "").trim();
    if (!name) return "name_required";
    if (name.length > 80) return "name_too_long";
    if (!category) return "category_required";
    if (category.length > 40) return "category_too_long";
    if (!location) return "location_required";
    if (location.length > 100) return "location_too_long";
    if (!contact) return "contact_required";
    if (contact.length > 80) return "contact_too_long";
    if (!isValidDate(report.date)) return "invalid_date";
    if (String(report.description || "").length > 300) return "description_too_long";
    return null;
  }

  function validateClaim(claim) {
    if (!claim) return "name_required";
    const name = String(claim.name || "").trim();
    const contact = String(claim.contact || "").trim();
    if (!name) return "name_required";
    if (name.length > 60) return "name_too_long";
    if (!contact) return "contact_required";
    if (contact.length > 80) return "contact_too_long";
    return null;
  }

  function canClaim(item, user) {
    return Boolean(user && item && item.status === "open" && item.userId !== user.id);
  }

  function canDeleteReport(item, user) {
    return Boolean(user && item && item.userId === user.id);
  }

  function reportState(item) {
    return item.status === "done" ? "returned" : item.type;
  }

  function filterReports(items, filter, query) {
    const search = String(query || "").trim().toLowerCase();
    return items.filter((item) => {
      const matchesFilter = filter === "all" || reportState(item) === filter;
      const searchableText = [item.name, item.location, item.description, item.category]
        .join(" ")
        .toLowerCase();
      return matchesFilter && (!search || searchableText.includes(search));
    });
  }

  function isValidDate(value) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(String(value || ""))) return false;
    const date = new Date(`${value}T00:00:00Z`);
    return !Number.isNaN(date.valueOf()) && date.toISOString().slice(0, 10) === value;
  }

  const api = { validateReport, validateClaim, canClaim, canDeleteReport, reportState, filterReports };
  root.LostFoundDomain = api;
  if (typeof module !== "undefined" && module.exports) module.exports = api;
})(typeof globalThis !== "undefined" ? globalThis : window);
