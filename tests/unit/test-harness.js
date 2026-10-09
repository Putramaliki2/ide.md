(function (root) {
  "use strict";

  const tests = [];

  function test(name, run) {
    tests.push({ name, run });
  }

  function equal(actual, expected) {
    if (actual !== expected) {
      throw new Error(`expected ${JSON.stringify(expected)}, got ${JSON.stringify(actual)}`);
    }
  }

  function deepEqual(actual, expected) {
    if (JSON.stringify(actual) !== JSON.stringify(expected)) {
      throw new Error(`expected ${JSON.stringify(expected)}, got ${JSON.stringify(actual)}`);
    }
  }

  function truthy(value) {
    if (!value) throw new Error(`expected a truthy value, got ${JSON.stringify(value)}`);
  }

  function falsy(value) {
    if (value) throw new Error(`expected a falsy value, got ${JSON.stringify(value)}`);
  }

  function finish() {
    const results = document.querySelector("#results");
    let passed = 0;
    for (const entry of tests) {
      const row = document.createElement("li");
      try {
        entry.run();
        row.className = "pass";
        row.textContent = `LULUS — ${entry.name}`;
        passed += 1;
      } catch (error) {
        row.className = "fail";
        row.textContent = `GAGAL — ${entry.name}: ${error.message}`;
      }
      results.append(row);
    }
    const summary = document.querySelector("#summary");
    summary.textContent = `${passed}/${tests.length} test lulus`;
    summary.className = passed === tests.length ? "pass" : "fail";
    document.title = `${passed === tests.length ? "LULUS" : "GAGAL"} — Unit Tests`;
  }

  root.TestHarness = { test, equal, deepEqual, truthy, falsy, finish };
})(window);
