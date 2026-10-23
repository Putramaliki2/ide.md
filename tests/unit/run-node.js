"use strict";

const path = require("node:path");

const entries = [];
const summary = { textContent: "", className: "" };
const results = {
  append(entry) {
    entries.push(entry);
  }
};
global.window = global;
global.document = {
  title: "",
  querySelector(selector) {
    return selector === "#summary" ? summary : results;
  },
  createElement() {
    return { className: "", textContent: "" };
  }
};

require("../../assets/lostfound-domain.js");
require("./test-harness.js");
[
  "report-validation.test.js",
  "report-search.test.js",
  "claim-eligibility.test.js",
  "claim-input-validation.test.js",
  "report-ownership-status.test.js"
].forEach((file) => require(path.join(__dirname, file)));

TestHarness.finish();
console.log(summary.textContent);
entries.forEach((entry) => console.log(entry.textContent));
if (summary.className !== "pass") process.exitCode = 1;
