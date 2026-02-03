import intlTelInput from "intl-tel-input";
import "intl-tel-input/build/css/intlTelInput.css";
import ru from "intl-tel-input/i18n/ru";

function initPhoneInputs() {
    const containers = document.querySelectorAll("[data-phone-input]");
    if (!containers.length) return;

    containers.forEach((container) => {
        const input = container.querySelector('input[type="tel"]');
        const hiddenPhone = container.querySelector('input[name="phone"]');
        const hiddenCountryId = container.querySelector(
            'input[name="phone_country_id"]'
        );
        const hiddenCountryCode = container.querySelector(
            'input[name="phone_country_code"]'
        );
        const sendCountryCode =
            (container.dataset.sendCountryCode || "") === "1";
        const internationalFormat =
            (container.dataset.internationalFormat || "") === "1";
        if (!input || !hiddenPhone || !hiddenCountryId) return;

        const initialPhone = (container.dataset.initialPhone || "").trim();
        let countryMap = {};
        try {
            const raw = container.dataset.countryMap;
            countryMap = raw ? JSON.parse(raw) : {};
        } catch (_) {}
        let onlyCountries = [];
        try {
            const raw = container.dataset.onlyCountries;
            onlyCountries = raw ? JSON.parse(raw) : [];
        } catch (_) {}
        if (!Array.isArray(onlyCountries)) onlyCountries = [];

        // По умолчанию Беларусь; если список задан — беларусь при наличии, иначе первый из списка
        const defaultCountry = "by";
        const firstCountry =
            onlyCountries.length > 0
                ? onlyCountries.includes(defaultCountry)
                    ? defaultCountry
                    : onlyCountries[0]
                : defaultCountry;
        const options = {
            loadUtils: () => import("intl-tel-input/utils"),
            initialCountry: firstCountry,
            nationalMode: !internationalFormat,
            countryNameLocale: "ru",
            i18n: ru,
            dropdownContainer: document.body,
            separateDialCode: !internationalFormat,
        };
        if (onlyCountries.length > 0) {
            options.onlyCountries = onlyCountries;
        }

        const iti = intlTelInput(input, options);

        // В режиме с отдельным кодом (флаг+код) у контейнера есть padding-left; добавляем к отступу инпута, чтобы номер не наезжал
        function addContainerPaddingToInput() {
            if (internationalFormat) return;
            const countryContainer = input
                .closest(".iti")
                ?.querySelector(".iti__country-container");
            if (!countryContainer) return;
            const style = getComputedStyle(countryContainer);
            const extraPx = parseFloat(style.paddingLeft) || 0;
            const currentPx = parseFloat(input.style.paddingLeft) || 0;
            input.style.paddingLeft = `${currentPx + extraPx}px`;
        }

        function syncHiddenFields() {
            const fullNumber = iti.getNumber();
            const selectedData = iti.getSelectedCountryData();
            const iso2 = selectedData?.iso2
                ? selectedData.iso2.toLowerCase()
                : "";
            const countryId = iso2 ? countryMap[iso2] || "" : "";

            if (fullNumber) {
                hiddenPhone.value = fullNumber;
                hiddenCountryId.value = countryId;
                if (sendCountryCode && hiddenCountryCode)
                    hiddenCountryCode.value = iso2;
            } else {
                hiddenPhone.value = "";
                hiddenCountryId.value = "";
                if (sendCountryCode && hiddenCountryCode)
                    hiddenCountryCode.value = "";
            }
        }

        iti.promise
            .then(() => {
                setTimeout(addContainerPaddingToInput, 0);
                if (initialPhone) {
                    iti.setNumber(initialPhone);
                }
                syncHiddenFields();
            })
            .catch(() => {
                setTimeout(addContainerPaddingToInput, 0);
                syncHiddenFields();
            });

        input.addEventListener("countrychange", () => {
            setTimeout(addContainerPaddingToInput, 0);
            syncHiddenFields();
        });
        input.addEventListener("input", syncHiddenFields);
        input.addEventListener("blur", syncHiddenFields);

        const form = container.closest("form");
        if (form) {
            form.addEventListener("submit", () => {
                syncHiddenFields();
            });
        }
    });
}

if (typeof document !== "undefined") {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initPhoneInputs);
    } else {
        initPhoneInputs();
    }
}

export default initPhoneInputs;
