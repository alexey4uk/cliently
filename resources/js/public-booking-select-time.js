/**
 * Публичная запись: выбор даты и времени (страница select-time).
 * Конфиг читается из <script id="public-booking-select-time-config" type="application/json">.
 */
document.addEventListener('DOMContentLoaded', function () {
  const configEl = document.getElementById('public-booking-select-time-config');
  if (!configEl) return;

  let config;
  try {
    config = JSON.parse(configEl.textContent);
  } catch {
    return;
  }

  const { datesWithSlots = {}, date: configDate, currentDateHasSlots } = config;
  const timeSlots = document.querySelectorAll('.time-slot-btn');
  const detailsBlock = document.getElementById('appointment-details');
  const timeInput = document.getElementById('selected-time-input');
  const timeDisplay = document.getElementById('summary-time');
  const dateText = document.getElementById('summary-date');
  const dateInput = document.getElementById('selected-date-input');
  const form = document.getElementById('appointment-form');
  const calendarGrid = document.getElementById('calendar-grid');
  const monthLabel = document.getElementById('current-month-year');
  const toggleBtn = document.getElementById('toggle-date-selector-btn');
  const container = document.getElementById('calendar-container');
  const scrollToCalendarBtn = document.getElementById('scroll-to-calendar-btn');

  const currentPageDate = new Date(configDate);
  currentPageDate.setHours(0, 0, 0, 0);

  let calendarDate = new Date(currentPageDate);

  // 1. Выбор времени
  timeSlots.forEach((btn) => {
    btn.addEventListener('click', function () {
      timeSlots.forEach((b) => b.classList.remove('selected'));
      this.classList.add('selected');

      const time = this.dataset.time;
      timeInput.value = time;
      timeDisplay.innerText = time;

      const d = new Date(dateInput.value);
      dateText.innerText = d.toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
      });

      detailsBlock.classList.remove('hidden');
      setTimeout(
        () =>
          detailsBlock.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
          }),
        100
      );
    });
  });

  // 2. Кнопка «Выбрать другую дату» — открыть календарь и прокрутить к нему
  if (scrollToCalendarBtn && container) {
    scrollToCalendarBtn.addEventListener('click', function () {
      if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        const icon = document.getElementById('date-selector-icon');
        if (icon) icon.classList.add('rotate-180');
        if (calendarGrid && monthLabel) updateCalendar();
      }
      const target = document.getElementById('toggle-date-selector-btn') || container;
      target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  }

  // 3. Календарь
  function updateCalendar() {
    const months = [
      'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
      'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь',
    ];
    monthLabel.textContent = `${months[calendarDate.getMonth()]} ${calendarDate.getFullYear()}`;
    calendarGrid.innerHTML = '';

    const firstDayOfMonth = new Date(calendarDate.getFullYear(), calendarDate.getMonth(), 1);
    const dayOfWeek = firstDayOfMonth.getDay();
    const daysToSubtract = dayOfWeek === 0 ? 6 : dayOfWeek - 1;

    const cellDate = new Date(firstDayOfMonth);
    cellDate.setDate(firstDayOfMonth.getDate() - daysToSubtract);
    cellDate.setHours(0, 0, 0, 0);

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let i = 0; i < 42; i++) {
      const year = cellDate.getFullYear();
      const month = String(cellDate.getMonth() + 1).padStart(2, '0');
      const day = String(cellDate.getDate()).padStart(2, '0');
      const cellDateString = `${year}-${month}-${day}`;

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.innerHTML = `<span class="relative z-10">${cellDate.getDate()}</span>`;

      const isCurrentMonth = cellDate.getMonth() === calendarDate.getMonth();
      const isPast = cellDate < today;
      const isToday = cellDate.getTime() === today.getTime();
      const isSelected = cellDate.getTime() === currentPageDate.getTime();
      const hasSlots = datesWithSlots[cellDateString] || false;

      const classList = [
        'relative', 'py-3', 'text-sm', 'rounded-xl', 'transition-all', 'flex',
        'flex-col', 'items-center', 'justify-center', 'font-bold', 'w-full', 'min-h-[44px]',
        'border-2', 'touch-manipulation',
      ];

      if (isSelected) {
        classList.push('border-indigo-500', 'bg-indigo-50/50', 'dark:bg-indigo-900/30');
      } else {
        classList.push('border-transparent');
      }

      if (isToday) {
        classList.push('text-indigo-600', 'dark:text-indigo-400');
        const dot = document.createElement('div');
        dot.className = 'absolute bottom-1 w-1 h-1 bg-indigo-600 dark:bg-indigo-400 rounded-full';
        btn.appendChild(dot);
      }

      if (!isCurrentMonth) {
        classList.push('text-slate-300', 'dark:text-slate-700', 'opacity-40', 'cursor-default');
        btn.disabled = true;
      } else if (isPast && !isToday) {
        classList.push('text-slate-400', 'dark:text-slate-600', 'opacity-40', 'cursor-not-allowed');
        btn.disabled = true;
      } else if (!hasSlots) {
        classList.push('text-slate-400', 'dark:text-slate-500', 'opacity-60', 'cursor-not-allowed');
        btn.disabled = true;
      } else {
        if (!isToday) classList.push('text-slate-700', 'dark:text-slate-200');
        classList.push('hover:bg-indigo-100', 'dark:hover:bg-indigo-800/40', 'cursor-pointer');
        btn.onclick = () => {
          const url = new URL(window.location.href);
          url.searchParams.set('date', cellDateString);
          window.location.href = url.toString();
        };
      }

      btn.className = classList.join(' ');
      calendarGrid.appendChild(btn);
      cellDate.setDate(cellDate.getDate() + 1);
    }
  }

  if (toggleBtn && container) {
    toggleBtn.addEventListener('click', () => {
      container.classList.toggle('hidden');
      const icon = document.getElementById('date-selector-icon');
      if (icon) icon.classList.toggle('rotate-180');
      if (!container.classList.contains('hidden')) {
        updateCalendar();
      }
    });
  }

  const prevBtn = document.getElementById('prev-month');
  const nextBtn = document.getElementById('next-month');
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      calendarDate.setMonth(calendarDate.getMonth() - 1);
      updateCalendar();
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      calendarDate.setMonth(calendarDate.getMonth() + 1);
      updateCalendar();
    });
  }

  // 4. Валидация и отправка
  if (form) {
    form.onsubmit = function (e) {
      if (!timeInput.value) {
        e.preventDefault();
        alert('Выберите время');
        return false;
      }
      const submitBtn = document.getElementById('submit-btn');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
      }
      return true;
    };
  }

  if (container && !container.classList.contains('hidden')) {
    updateCalendar();
  }
});
