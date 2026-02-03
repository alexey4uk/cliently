import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';
import ru from 'intl-tel-input/i18n/ru';

function initPhoneInputs() {
    const containers = document.querySelectorAll('[data-phone-input]');
    if (!containers.length) return;

    containers.forEach((container) => {
        const input = container.querySelector('input[type="tel"]');
        const hiddenPhone = container.querySelector('input[name="phone"]');
        const hiddenCountryId = container.querySelector('input[name="phone_country_id"]');
        if (!input || !hiddenPhone || !hiddenCountryId) return;

        const initialPhone = (container.dataset.initialPhone || '').trim();
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

        // Когда список пустой — не передаём onlyCountries, библиотека покажет все страны
        const firstCountry = onlyCountries.length > 0 ? onlyCountries[0] : 'ru';
        const options = {
            loadUtils: () => import('intl-tel-input/utils'),
            initialCountry: firstCountry,
            nationalMode: true,
            countryNameLocale: 'ru',
            i18n: ru,
            dropdownContainer: document.body,
            separateDialCode: true,
        };
        if (onlyCountries.length > 0) {
            options.onlyCountries = onlyCountries;
        }

        const iti = intlTelInput(input, options);

        // Библиотека считает только selectedCountry.offsetWidth и не учитывает padding контейнера.
        // Добавляем к padding-left инпута горизонтальный padding нашего .iti__country-container.
        function applyContainerPaddingToInput() {
            const countryContainer = input.closest('.iti')?.querySelector('.iti__country-container');
            if (!countryContainer) return;
            const style = getComputedStyle(countryContainer);
            const extraPx = (parseFloat(style.paddingLeft) || 0) + (parseFloat(style.paddingRight) || 0);
            const currentPx = parseFloat(input.style.paddingLeft) || 0;
            input.style.setProperty('padding-left', `${currentPx + extraPx}px`, 'important');
        }

        function syncHiddenFields() {
            const fullNumber = iti.getNumber();
            const selectedData = iti.getSelectedCountryData();
            const iso2 = selectedData?.iso2 ? selectedData.iso2.toLowerCase() : '';
            const countryId = iso2 ? (countryMap[iso2] || '') : '';

            if (fullNumber) {
                hiddenPhone.value = fullNumber;
                hiddenCountryId.value = countryId;
            } else {
                hiddenPhone.value = '';
                hiddenCountryId.value = '';
            }
        }

        iti.promise.then(() => {
            setTimeout(applyContainerPaddingToInput, 0);
            if (initialPhone) {
                iti.setNumber(initialPhone);
            }
            syncHiddenFields();
        }).catch(() => {
            setTimeout(applyContainerPaddingToInput, 0);
            syncHiddenFields();
        });

        input.addEventListener('countrychange', () => {
            setTimeout(applyContainerPaddingToInput, 0);
            syncHiddenFields();
        });
        input.addEventListener('input', syncHiddenFields);
        input.addEventListener('blur', syncHiddenFields);

        const form = container.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                syncHiddenFields();
            });
        }
    });
}

if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPhoneInputs);
    } else {
        initPhoneInputs();
    }
}

export default initPhoneInputs;
