/**
 * Автопоиск адреса через Nominatim (OpenStreetMap), бесплатный API.
 * Русский язык через заголовок Accept-Language: ru. Ограничение: 1 запрос/сек (debounce 1.1 с).
 * Режимы:
 * 1) Одно поле "Поиск адреса" [data-address-autocomplete] — при выборе заполняются city, street, house.
 * 2) Подсказки у каждого поля: data-address-field="city|street|house".
 */

const NOMINATIM_URL = "https://nominatim.openstreetmap.org/search";
const DEBOUNCE_MS = 1100; // Nominatim: max 1 req/sec
const LIMIT = 8;
const ALLOWED_COUNTRY_CODES = ["ru", "by"];

function nominatimToFeature(item) {
    const a = item.address || {};
    const city =
        a.city || a.town || a.village || a.municipality || a.state || "";
    const street = a.road || "";
    const housenumber = a.house_number || "";
    const country = a.country || "";
    const countrycode = (a.country_code || "").toLowerCase();
    return {
        properties: {
            name: item.display_name,
            city,
            street,
            housenumber,
            country,
            countrycode,
        },
    };
}

function fetchAddressSuggestions(query) {
    const params = new URLSearchParams({
        q: query,
        format: "json",
        addressdetails: 1,
        limit: LIMIT * 2,
        countrycodes: ALLOWED_COUNTRY_CODES.join(","),
    });
    return fetch(`${NOMINATIM_URL}?${params}`, {
        headers: {
            "Accept-Language": "ru",
            "User-Agent": "Cliently/1.0 (address autocomplete)",
        },
    })
        .then((res) => res.json())
        .then((items) => {
            return (items || [])
                .filter((item) => {
                    const code = (
                        item.address?.country_code || ""
                    ).toLowerCase();
                    return ALLOWED_COUNTRY_CODES.includes(code);
                })
                .map(nominatimToFeature);
        });
}

function formatFullAddress(feature) {
    const p = feature.properties || {};
    const parts = [
        p.street && p.housenumber
            ? `${p.street}, ${p.housenumber}`
            : p.street || p.name,
        p.city,
        p.country,
    ].filter(Boolean);
    return parts.join(", ") || p.name || "Адрес";
}

function createDropdownStyles() {
    return "w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border-b border-slate-100 dark:border-slate-700 last:border-0 transition-colors";
}

function initSingleSearch(wrapper) {
    const input = wrapper.querySelector('input[type="text"]');
    const listEl = wrapper.querySelector("[data-address-suggestions]");
    const form = wrapper.closest("form");
    if (!input || !listEl || !form) return;

    let debounceTimer = null;

    function hide() {
        listEl.innerHTML = "";
        listEl.classList.add("hidden");
    }

    function show(items) {
        listEl.innerHTML = "";
        if (!items.length) {
            listEl.classList.add("hidden");
            return;
        }
        items.slice(0, LIMIT).forEach((feature) => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = createDropdownStyles();
            btn.textContent = formatFullAddress(feature);
            btn.addEventListener("click", () => {
                const p = feature.properties || {};
                const cityInput = form.querySelector('input[name="city"]');
                const streetInput = form.querySelector('input[name="street"]');
                const houseInput = form.querySelector('input[name="house"]');
                const buildingInput = form.querySelector(
                    'input[name="building"]'
                );
                const addressInput = form.querySelector(
                    'input[name="address"]'
                );
                if (cityInput) cityInput.value = p.city || p.name || "";
                if (streetInput) streetInput.value = p.street || "";
                if (houseInput) houseInput.value = p.housenumber || "";
                if (addressInput)
                    addressInput.value = formatFullAddress(feature);
                input.value = formatFullAddress(feature);
                hide();
                form.dispatchEvent(new Event("input", { bubbles: true }));
                form.dispatchEvent(new CustomEvent("address-selected"));
            });
            listEl.appendChild(btn);
        });
        listEl.classList.remove("hidden");
    }

    input.addEventListener("input", () => {
        const q = input.value.trim();
        if (q.length < 2) {
            hide();
            return;
        }
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchAddressSuggestions(q)
                .then((features) => show(features))
                .catch(() => hide());
        }, DEBOUNCE_MS);
    });

    input.addEventListener("blur", () => setTimeout(hide, 200));
}

function initPerFieldAutocomplete(form) {
    const cityInput = form.querySelector(
        'input[name="city"][data-address-field="city"]'
    );
    const streetInput = form.querySelector(
        'input[name="street"][data-address-field="street"]'
    );
    const houseInput = form.querySelector(
        'input[name="house"][data-address-field="house"]'
    );

    function getCity() {
        const el = form.querySelector('input[name="city"]');
        return el ? el.value.trim() : "";
    }
    function getStreet() {
        const el = form.querySelector('input[name="street"]');
        return el ? el.value.trim() : "";
    }

    function attachDropdown(input, getQuery, formatItem, onSelect) {
        if (!input) return;
        let dropdown = input.nextElementSibling;
        if (
            !dropdown ||
            dropdown.getAttribute("data-address-suggestions") === null
        ) {
            dropdown = document.createElement("div");
            dropdown.setAttribute("data-address-suggestions", "");
            dropdown.className =
                "hidden absolute left-0 right-0 top-full z-20 mt-1 max-h-52 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg";
            dropdown.setAttribute("role", "listbox");
            input.parentNode.appendChild(dropdown);
        }

        let debounceTimer = null;

        function hide() {
            dropdown.innerHTML = "";
            dropdown.classList.add("hidden");
        }

        function show(items) {
            dropdown.innerHTML = "";
            if (!items.length) {
                dropdown.classList.add("hidden");
                return;
            }
            items.forEach((item) => {
                const btn = document.createElement("button");
                btn.type = "button";
                btn.className = createDropdownStyles();
                btn.textContent = formatItem(item);
                btn.addEventListener("click", () => {
                    onSelect(item);
                    hide();
                    form.dispatchEvent(new Event("input", { bubbles: true }));
                    form.dispatchEvent(new CustomEvent("address-selected"));
                });
                dropdown.appendChild(btn);
            });
            dropdown.classList.remove("hidden");
        }

        input.addEventListener("input", () => {
            const q = getQuery();
            if (q.length < 2) {
                hide();
                return;
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchAddressSuggestions(q)
                    .then((features) => {
                        if (
                            input.getAttribute("data-address-field") === "city"
                        ) {
                            const seen = new Set();
                            const unique = features.filter((f) => {
                                const key = (
                                    f.properties?.city ||
                                    f.properties?.name ||
                                    ""
                                )
                                    .trim()
                                    .toLowerCase();
                                if (!key || seen.has(key)) return false;
                                seen.add(key);
                                return true;
                            });
                            show(unique);
                        } else {
                            show(features);
                        }
                    })
                    .catch(() => hide());
            }, DEBOUNCE_MS);
        });

        input.addEventListener("focus", () => {
            if (dropdown.children.length) dropdown.classList.remove("hidden");
        });
        input.addEventListener("blur", () => setTimeout(hide, 200));
    }

    if (cityInput) {
        attachDropdown(
            cityInput,
            () => cityInput.value.trim(),
            (f) =>
                (f.properties?.city || f.properties?.name || "").trim() ||
                "Город",
            (f) => {
                const p = f.properties || {};
                cityInput.value = p.city || p.name || "";
            }
        );
    }

    if (streetInput) {
        attachDropdown(
            streetInput,
            () => {
                const city = getCity();
                const street = streetInput.value.trim();
                return city ? `${city} ${street}` : street;
            },
            (f) => {
                const p = f.properties || {};
                if (p.street && p.housenumber)
                    return `${p.street}, ${p.housenumber}`;
                return p.street || p.name || "Улица";
            },
            (f) => {
                const p = f.properties || {};
                streetInput.value = p.street || "";
                const houseEl = form.querySelector('input[name="house"]');
                if (houseEl) houseEl.value = p.housenumber || houseEl.value;
            }
        );
    }

    if (houseInput) {
        attachDropdown(
            houseInput,
            () => {
                const city = getCity();
                const street = getStreet();
                const house = houseInput.value.trim();
                return [city, street, house].filter(Boolean).join(" ");
            },
            (f) => formatFullAddress(f),
            (f) => {
                const p = f.properties || {};
                houseInput.value = p.housenumber || houseInput.value;
                const buildingInput = form.querySelector(
                    'input[name="building"]'
                );
                if (buildingInput && p.housenumber)
                    buildingInput.value = buildingInput.value || "";
            }
        );
    }
}

function initAddressAutocomplete() {
    document
        .querySelectorAll("[data-address-autocomplete]")
        .forEach(initSingleSearch);

    document.querySelectorAll("form").forEach((form) => {
        if (form.querySelector("input[data-address-field]")) {
            initPerFieldAutocomplete(form);
        }
    });
}

if (typeof document !== "undefined") {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initAddressAutocomplete);
    } else {
        initAddressAutocomplete();
    }
}

export default initAddressAutocomplete;
