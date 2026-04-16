import home2Image from "../images/home2.jpg";
import home3Image from "../images/home3.jpg";
import home4Image from "../images/home4.jpg";

import.meta.glob("../images/**/*", {
    eager: true,
    query: "?url",
    import: "default",
});

void home2Image;
void home3Image;
void home4Image;

import jQuery from "jquery";

window.$ = window.jQuery = jQuery;

const applyBackgroundImages = () => {
    document.querySelectorAll("[data-bg-image]").forEach((element) => {
        const backgroundImage = element.getAttribute("data-bg-image");

        if (backgroundImage) {
            element.style.backgroundImage = `url("${backgroundImage}")`;
        }
    });
};

const hideLoader = () => {
    document.getElementById("ftco-loader")?.classList.remove("show");
};

const DAY_IN_MS = 24 * 60 * 60 * 1000;

const getDateAtMidnight = (value = new Date()) => {
    const date = value instanceof Date ? new Date(value) : new Date(value);

    if (Number.isNaN(date.getTime())) {
        return null;
    }

    date.setHours(0, 0, 0, 0);
    return date;
};

const addDays = (value, days) => {
    const date = getDateAtMidnight(value);

    if (!date) {
        return null;
    }

    const nextDate = new Date(date);
    nextDate.setDate(nextDate.getDate() + days);
    return nextDate;
};

const parseDateValue = (value) => {
    if (!value) {
        return null;
    }

    if (value instanceof Date) {
        return getDateAtMidnight(value);
    }

    const normalizedValue = String(value).trim();
    let match = normalizedValue.match(/^(\d{4})-(\d{2})-(\d{2})$/);

    if (match) {
        const [, year, month, day] = match;
        return getDateAtMidnight(
            new Date(Number(year), Number(month) - 1, Number(day)),
        );
    }

    match = normalizedValue.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);

    if (match) {
        const [, day, month, year] = match;
        return getDateAtMidnight(
            new Date(Number(year), Number(month) - 1, Number(day)),
        );
    }

    return getDateAtMidnight(normalizedValue);
};

const formatIsoDate = (value) => {
    const date = getDateAtMidnight(value);

    if (!date) {
        return "";
    }

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
};

const formatDisplayDate = (value) => {
    const date = parseDateValue(value);

    if (!date) {
        return "--/--/----";
    }

    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
};

const formatGuestSummary = (adults, children) =>
    `${adults} người lớn - ${children} trẻ em`;

const enhanceBookingForm = () => {
    const bookingForm = document.querySelector(".ftco-booking .booking-form");

    if (!bookingForm) {
        return;
    }

    const bookingColumns = bookingForm.querySelectorAll(".row > .col-md");

    if (bookingColumns.length < 5) {
        return;
    }

    const roomColumn = bookingColumns[2];
    const guestColumn = bookingColumns[3];
    const searchColumn = bookingColumns[4];

    roomColumn.classList.add("booking-room-field");

    const checkinLabel = bookingColumns[0].querySelector("label");
    const checkoutLabel = bookingColumns[1].querySelector("label");
    const checkinInput = bookingColumns[0].querySelector(".checkin_date");
    const checkoutInput = bookingColumns[1].querySelector(".checkout_date");

    if (checkinLabel) {
        checkinLabel.textContent = "Ngày nhận";
    }

    if (checkoutLabel) {
        checkoutLabel.textContent = "Ngày trả";
    }

    if (checkinInput instanceof HTMLInputElement) {
        checkinInput.id = "booking-checkin";
        checkinInput.classList.add("booking-input");
        checkinInput.placeholder = "Chọn ngày";
        checkinInput.autocomplete = "off";
    }

    if (checkoutInput instanceof HTMLInputElement) {
        checkoutInput.id = "booking-checkout";
        checkoutInput.classList.add("booking-input");
        checkoutInput.placeholder = "Chọn ngày";
        checkoutInput.autocomplete = "off";
    }

    const guestWrap = guestColumn.querySelector(".wrap");

    if (guestWrap) {
        guestWrap.innerHTML = `
            <label for="guest-trigger">Số khách</label>
            <div class="booking-guests" data-guest-picker>
                <input type="hidden" name="adults" value="2" data-guest-input="adults">
                <input type="hidden" name="children" value="0" data-guest-input="children">
                <input type="hidden" name="rooms" value="1" data-guest-input="rooms">
                <button
                    type="button"
                    id="guest-trigger"
                    class="guest-trigger"
                    data-guest-trigger
                    aria-expanded="false"
                    aria-haspopup="dialog"
                >
                    <span class="guest-trigger-icon">
                        <span class="ion-ios-person-outline"></span>
                    </span>
                    <span class="guest-trigger-text" data-guest-summary>${formatGuestSummary(2, 0)}</span>
                    <span class="guest-trigger-arrow">
                        <span class="ion-ios-arrow-down"></span>
                    </span>
                </button>
                <div class="guest-dropdown" data-guest-dropdown hidden>
                    <div class="guest-row">
                        <div class="guest-row-copy">
                            <div class="guest-row-title">Người lớn</div>
                        </div>
                        <div class="guest-stepper">
                            <button type="button" class="guest-stepper-btn" data-counter-action="decrement" data-counter-target="adults" aria-label="Giảm số người lớn">-</button>
                            <span class="guest-stepper-value" data-guest-count="adults">2</span>
                            <button type="button" class="guest-stepper-btn" data-counter-action="increment" data-counter-target="adults" aria-label="Tăng số người lớn">+</button>
                        </div>
                    </div>
                    <div class="guest-row">
                        <div class="guest-row-copy">
                            <div class="guest-row-title">Trẻ em</div>
                        </div>
                        <div class="guest-stepper">
                            <button type="button" class="guest-stepper-btn" data-counter-action="decrement" data-counter-target="children" aria-label="Giảm số trẻ em">-</button>
                            <span class="guest-stepper-value" data-guest-count="children">0</span>
                            <button type="button" class="guest-stepper-btn" data-counter-action="increment" data-counter-target="children" aria-label="Tăng số trẻ em">+</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    const searchAction = searchColumn.querySelector(".btn");

    if (searchAction) {
        searchAction.innerHTML = `<span class="booking-submit-content"><i class="ion-ios-search"></i>Tìm kiếm</span>`;
    }
};

const initGuestPickers = () => {
    document.querySelectorAll("[data-guest-picker]").forEach((picker) => {
        const trigger = picker.querySelector("[data-guest-trigger]");
        const dropdown = picker.querySelector("[data-guest-dropdown]");
        const summary = picker.querySelector("[data-guest-summary]");
        const inputs = {
            adults: picker.querySelector('[data-guest-input="adults"]'),
            children: picker.querySelector('[data-guest-input="children"]'),
            rooms: picker.querySelector('[data-guest-input="rooms"]'),
        };
        const counts = {
            adults: picker.querySelector('[data-guest-count="adults"]'),
            children: picker.querySelector('[data-guest-count="children"]'),
        };
        const controls = picker.querySelectorAll("[data-counter-action]");
        const limits = {
            adults: { min: 1, max: 10 },
            children: { min: 0, max: 6 },
        };

        if (
            !(trigger instanceof HTMLButtonElement) ||
            !(dropdown instanceof HTMLDivElement) ||
            !(summary instanceof HTMLSpanElement)
        ) {
            return;
        }

        const getCount = (key) => {
            const input = inputs[key];

            if (!(input instanceof HTMLInputElement)) {
                return 0;
            }

            return Number.parseInt(input.value || "0", 10);
        };

        const setCount = (key, value) => {
            const input = inputs[key];

            if (input instanceof HTMLInputElement) {
                input.value = String(value);
            }
        };

        const update = () => {
            const adults = getCount("adults");
            const children = getCount("children");
            const rooms = Math.max(getCount("rooms"), 1);

            if (counts.adults instanceof HTMLSpanElement) {
                counts.adults.textContent = String(adults);
            }

            if (counts.children instanceof HTMLSpanElement) {
                counts.children.textContent = String(children);
            }

            summary.textContent = formatGuestSummary(adults, children, rooms);

            controls.forEach((control) => {
                if (!(control instanceof HTMLButtonElement)) {
                    return;
                }

                const action = control.dataset.counterAction;
                const target = control.dataset.counterTarget;

                if (!target || !(target in limits)) {
                    return;
                }

                const currentValue = getCount(target);
                const { min, max } = limits[target];

                control.disabled =
                    (action === "decrement" && currentValue <= min) ||
                    (action === "increment" && currentValue >= max);
            });
        };

        const open = () => {
            picker.classList.add("is-open");
            dropdown.hidden = false;
            trigger.setAttribute("aria-expanded", "true");
        };

        const close = () => {
            picker.classList.remove("is-open");
            dropdown.hidden = true;
            trigger.setAttribute("aria-expanded", "false");
        };

        trigger.addEventListener("click", (event) => {
            event.preventDefault();

            if (dropdown.hidden) {
                open();
                return;
            }

            close();
        });

        controls.forEach((control) => {
            if (!(control instanceof HTMLButtonElement)) {
                return;
            }

            control.addEventListener("click", () => {
                const action = control.dataset.counterAction;
                const target = control.dataset.counterTarget;

                if (!target || !(target in limits)) {
                    return;
                }

                const currentValue = getCount(target);
                const nextValue =
                    action === "increment"
                        ? Math.min(currentValue + 1, limits[target].max)
                        : Math.max(currentValue - 1, limits[target].min);

                setCount(target, nextValue);
                update();
            });
        });

        document.addEventListener("click", (event) => {
            if (!picker.contains(event.target)) {
                close();
            }
        });

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                close();
            }
        });

        update();
    });
};

const initRoomQuantitySteppers = () => {
    document.querySelectorAll("[data-room-qty-stepper]").forEach((stepper) => {
        const input = stepper.querySelector("[data-room-qty-input]");
        const decrementButton = stepper.querySelector('[data-room-qty-action="decrement"]');
        const incrementButton = stepper.querySelector('[data-room-qty-action="increment"]');

        if (
            !(input instanceof HTMLInputElement) ||
            !(decrementButton instanceof HTMLButtonElement) ||
            !(incrementButton instanceof HTMLButtonElement)
        ) {
            return;
        }

        const min = Number.parseInt(input.min || "1", 10);
        const max = Number.parseInt(input.max || "1", 10);

        const update = () => {
            const currentValue = Number.parseInt(input.value || String(min), 10);
            const normalizedValue = Math.min(Math.max(currentValue, min), max);

            input.value = String(normalizedValue);
            decrementButton.disabled = normalizedValue <= min;
            incrementButton.disabled = normalizedValue >= max;
        };

        decrementButton.addEventListener("click", () => {
            input.value = String(Number.parseInt(input.value || String(min), 10) - 1);
            update();
        });

        incrementButton.addEventListener("click", () => {
            input.value = String(Number.parseInt(input.value || String(min), 10) + 1);
            update();
        });

        update();
    });
};

const initRoomSliders = () => {
    document.querySelectorAll("[data-room-slider]").forEach((slider) => {
        const slides = Array.from(slider.querySelectorAll(".room-result-slide"));

        if (slides.length <= 1) {
            return;
        }

        let index = slides.findIndex((slide) => slide.classList.contains("is-active"));

        if (index < 0) {
            index = 0;
            slides[0].classList.add("is-active");
        }

        const show = (nextIndex) => {
            slides[index].classList.remove("is-active");
            index = (nextIndex + slides.length) % slides.length;
            slides[index].classList.add("is-active");
        };

        slider
            .querySelector("[data-room-slider-prev]")
            ?.addEventListener("click", () => show(index - 1));
        slider
            .querySelector("[data-room-slider-next]")
            ?.addEventListener("click", () => show(index + 1));
    });
};

const initRoomAmenitiesModal = () => {
    const modal = document.querySelector("[data-room-modal]");
    if (!modal) {
        return;
    }

    const title = modal.querySelector("[data-room-modal-title]");
    const area = modal.querySelector("[data-room-modal-area]");
    const desc = modal.querySelector("[data-room-modal-desc]");
    const slidesContainer = modal.querySelector("[data-room-modal-slides]");
    const prevButton = modal.querySelector("[data-room-modal-prev]");
    const nextButton = modal.querySelector("[data-room-modal-next]");
    let modalSlides = [];
    let modalIndex = 0;

    const renderSlides = (urls) => {
        if (!slidesContainer) {
            return;
        }

        slidesContainer.innerHTML = "";
        modalSlides = urls.map((url, idx) => {
            const slide = document.createElement("div");
            slide.className = "room-amenities-modal-slide";
            if (idx === 0) {
                slide.classList.add("is-active");
            }
            slide.style.backgroundImage = `url("${url}")`;
            slidesContainer.appendChild(slide);
            return slide;
        });
        modalIndex = 0;
    };

    const showModalSlide = (nextIndex) => {
        if (!modalSlides.length) {
            return;
        }
        modalSlides[modalIndex].classList.remove("is-active");
        modalIndex = (nextIndex + modalSlides.length) % modalSlides.length;
        modalSlides[modalIndex].classList.add("is-active");
    };

    prevButton?.addEventListener("click", () => showModalSlide(modalIndex - 1));
    nextButton?.addEventListener("click", () => showModalSlide(modalIndex + 1));

    const openModal = (trigger) => {
        title.textContent = trigger.dataset.roomTitle || "Phòng";
        area.textContent = trigger.dataset.roomArea || "";
        desc.textContent = trigger.dataset.roomDesc || "";

        const images = (trigger.dataset.roomImages || "")
            .split("|")
            .map((item) => item.trim())
            .filter(Boolean);
        renderSlides(images);

        modal.classList.add("is-open");
        document.body.classList.add("modal-open");
    };

    const closeModal = () => {
        modal.classList.remove("is-open");
        document.body.classList.remove("modal-open");
    };

    document.querySelectorAll("[data-room-modal-trigger]").forEach((trigger) => {
        trigger.addEventListener("click", () => openModal(trigger));
    });

    modal.querySelectorAll("[data-room-modal-close]").forEach((btn) => {
        btn.addEventListener("click", closeModal);
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && modal.classList.contains("is-open")) {
            closeModal();
        }
    });
};

const initRoomSelection = () => {
    const summary = document.querySelector("[data-booking-summary]");
    const totalEl = document.querySelector("[data-booking-total]");
    const listEl = document.querySelector("[data-booking-list]");

    if (!summary || !totalEl || !listEl) {
        return;
    }

    const getNights = () =>
        Number.parseInt(summary.dataset.nights || "1", 10) || 1;
    const selections = new Map();

    const renderList = () => {
        listEl.innerHTML = "";

        let total = 0;
        const nights = Math.max(getNights(), 1);

        selections.forEach((item) => {
            total += item.price * item.quantity * nights;

            const wrapper = document.createElement("div");
            wrapper.className = "booking-item";
            wrapper.innerHTML = `
                <div class="booking-item-title">Phòng: ${item.quantity} ${item.name}</div>
                <div class="booking-item-footer">
                  <div class="booking-item-price">${item.price.toLocaleString("vi-VN")} VND / đêm</div>
                  <button type="button" class="booking-item-cancel" data-room-cancel>
                    <span class="icon ion-ios-close"></span> Hủy
                  </button>
                </div>
            `;

            wrapper.querySelector("[data-room-cancel]")?.addEventListener("click", () => {
                selections.delete(item.name);
                renderList();
            });

            listEl.appendChild(wrapper);
        });

        totalEl.textContent = `${total.toLocaleString("vi-VN")} VND`;
    };

    document.querySelectorAll("[data-room-select]").forEach((button) => {
        button.addEventListener("click", () => {
            const card = button.closest(".room-result-card");
            const qtySelect = card?.querySelector("[data-room-qty]");
            const quantity = Number.parseInt(qtySelect?.value || "1", 10);
            const price = Number.parseInt(button.dataset.roomPrice || "0", 10);
            const roomName = button.dataset.roomName || "Phòng";

            if (quantity <= 0) {
                selections.delete(roomName);
            } else {
                selections.set(roomName, { name: roomName, price, quantity });
            }

            renderList();
        });
    });
};

const initSearchSummaryControls = () => {
    const summary = document.querySelector("[data-booking-summary]");
    const dateLine = document.querySelector("[data-booking-dates]");
    const checkinInput = document.querySelector("[data-search-checkin]");
    const checkoutInput = document.querySelector("[data-search-checkout]");
    const guestWrapper = document.querySelector("[data-search-guest]");
    const guestTrigger = document.querySelector("[data-search-guest-trigger]");
    const guestPanel = document.querySelector("[data-search-guest-panel]");
    const guestText = document.querySelector("[data-search-guest-text]");

    if (!summary || !dateLine || !checkinInput || !checkoutInput) {
        return;
    }

    const today = getDateAtMidnight(new Date());
    const guestLimits = {
        adults: { min: 1, max: 10 },
        children: { min: 0, max: 6 },
    };
    let isSyncingDates = false;

    const calcNights = (checkinValue, checkoutValue) => {
        const checkinDate = parseDateValue(checkinValue);
        const checkoutDate = parseDateValue(checkoutValue);

        if (!checkinDate || !checkoutDate) {
            return 1;
        }

        const diff = Math.round((checkoutDate - checkinDate) / DAY_IN_MS);
        return Math.max(diff, 1);
    };

    const syncSearchDates = () => {
        if (isSyncingDates) {
            return;
        }

        isSyncingDates = true;

        let checkinDate = parseDateValue(checkinInput.value);
        if (!checkinDate || (today && checkinDate < today)) {
            checkinDate = today;
        }

        const minCheckoutDate = addDays(checkinDate, 1);
        let checkoutDate = parseDateValue(checkoutInput.value);

        if (!checkoutDate || (minCheckoutDate && checkoutDate < minCheckoutDate)) {
            checkoutDate = minCheckoutDate;
        }

        if (today) {
            checkinInput.min = formatIsoDate(today);
        }

        if (minCheckoutDate) {
            checkoutInput.min = formatIsoDate(minCheckoutDate);
        }

        const normalizedCheckin = formatIsoDate(checkinDate);
        const normalizedCheckout = formatIsoDate(checkoutDate);

        if (normalizedCheckin) {
            checkinInput.value = normalizedCheckin;
        }

        if (normalizedCheckout) {
            checkoutInput.value = normalizedCheckout;
        }

        checkinInput.setCustomValidity("");
        checkoutInput.setCustomValidity("");
        isSyncingDates = false;
    };

    const updateGuestButtons = () => {
        guestWrapper?.querySelectorAll("[data-guest-action]").forEach((button) => {
            const type = button.dataset.guestType;
            const limit = type ? guestLimits[type] : null;
            const counter = type
                ? guestWrapper.querySelector(`[data-guest-count="${type}"]`)
                : null;

            if (!limit || !counter) {
                return;
            }

            const current = Number.parseInt(counter.textContent || "0", 10);
            button.disabled =
                button.dataset.guestAction === "dec"
                    ? current <= limit.min
                    : current >= limit.max;
        });
    };

    const updateSummary = () => {
        syncSearchDates();

        const checkinValue = checkinInput.value;
        const checkoutValue = checkoutInput.value;
        const nights = calcNights(checkinValue, checkoutValue);
        const days = nights + 1;

        summary.dataset.nights = String(nights);
        summary.dataset.checkin = checkinValue;

        if (guestWrapper) {
            const adults = guestWrapper.querySelector("[data-guest-count=\"adults\"]")?.textContent || "1";
            const children = guestWrapper.querySelector("[data-guest-count=\"children\"]")?.textContent || "0";
            summary.dataset.adults = adults;
            summary.dataset.children = children;
            if (guestText) {
                guestText.textContent = `${adults} người lớn - ${children} trẻ em`;
            }
        }

        dateLine.textContent = `${formatDisplayDate(checkinValue)} - ${formatDisplayDate(checkoutValue)} (${days} ngày ${nights} đêm)`;
        updateGuestButtons();
    };

    updateSummary();
    checkinInput.addEventListener("change", updateSummary);
    checkoutInput.addEventListener("change", updateSummary);

    if (guestTrigger && guestPanel) {
        guestTrigger.addEventListener("click", (event) => {
            event.stopPropagation();
            guestPanel.classList.toggle("is-open");
        });

        document.addEventListener("click", (event) => {
            if (!guestWrapper.contains(event.target)) {
                guestPanel.classList.remove("is-open");
            }
        });
    }

    guestWrapper?.querySelectorAll("[data-guest-action]").forEach((button) => {
        button.addEventListener("click", () => {
            const type = button.dataset.guestType;
            const limit = type ? guestLimits[type] : null;
            const counter = guestWrapper.querySelector(`[data-guest-count="${type}"]`);

            if (!limit || !counter) {
                return;
            }

            const current = Number.parseInt(counter.textContent || "0", 10);
            const nextValue =
                button.dataset.guestAction === "inc"
                    ? Math.min(current + 1, limit.max)
                    : Math.max(limit.min, current - 1);

            counter.textContent = String(nextValue);
            updateSummary();
        });
    });
};

const initVietnameseDatepicker = () => {
    if (!window.$ || !window.$.fn || !window.$.fn.datepicker) {
        return;
    }

    const $ = window.$;

    $.fn.datepicker.dates.vi = {
        days: [
            "Chủ nhật",
            "Thứ hai",
            "Thứ ba",
            "Thứ tư",
            "Thứ năm",
            "Thứ sáu",
            "Thứ bảy",
        ],
        daysShort: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
        daysMin: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
        months: [
            "Tháng 1",
            "Tháng 2",
            "Tháng 3",
            "Tháng 4",
            "Tháng 5",
            "Tháng 6",
            "Tháng 7",
            "Tháng 8",
            "Tháng 9",
            "Tháng 10",
            "Tháng 11",
            "Tháng 12",
        ],
        monthsShort: [
            "Th1",
            "Th2",
            "Th3",
            "Th4",
            "Th5",
            "Th6",
            "Th7",
            "Th8",
            "Th9",
            "Th10",
            "Th11",
            "Th12",
        ],
        today: "Hôm nay",
        clear: "Xóa",
        format: "dd/mm/yyyy",
        titleFormat: "MM yyyy",
        weekStart: 1,
    };

    const checkinInput = document.querySelector(".checkin_date");
    const checkoutInput = document.querySelector(".checkout_date");

    if (
        !(checkinInput instanceof HTMLInputElement) ||
        !(checkoutInput instanceof HTMLInputElement)
    ) {
        return;
    }

    const today = getDateAtMidnight(new Date());
    const $checkin = $(checkinInput);
    const $checkout = $(checkoutInput);
    const commonOptions = {
        format: "dd/mm/yyyy",
        autoclose: true,
        language: "vi",
        weekStart: 1,
        todayHighlight: true,
    };
    let isSyncingDates = false;

    $checkin.datepicker("destroy");
    $checkout.datepicker("destroy");
    $checkin.datepicker({
        ...commonOptions,
        startDate: today,
    });
    $checkout.datepicker({
        ...commonOptions,
        startDate: addDays(today, 1),
    });

    const syncDates = () => {
        if (isSyncingDates) {
            return;
        }

        isSyncingDates = true;

        let checkinDate = parseDateValue(checkinInput.value);
        if (!checkinDate || (today && checkinDate < today)) {
            checkinDate = today;
        }

        const minCheckoutDate = addDays(checkinDate, 1);
        let checkoutDate = parseDateValue(checkoutInput.value);

        if (!checkoutDate || (minCheckoutDate && checkoutDate < minCheckoutDate)) {
            checkoutDate = minCheckoutDate;
        }

        $checkin.datepicker("setStartDate", today);
        $checkout.datepicker("setStartDate", minCheckoutDate);
        $checkin.datepicker("setDate", checkinDate);
        $checkout.datepicker("setDate", checkoutDate);

        checkinInput.setCustomValidity("");
        checkoutInput.setCustomValidity("");
        isSyncingDates = false;
    };

    $checkin.on("changeDate", syncDates);
    $checkout.on("changeDate", syncDates);

    [checkinInput, checkoutInput].forEach((input) => {
        input.addEventListener("change", syncDates);
        input.addEventListener("focus", () => {
            window.$(input).datepicker("show");
        });
    });

    syncDates();
};

const initNativeDatePickers = () => {
    document
        .querySelectorAll(
            'input[type="date"][data-search-checkin], input[type="date"][data-search-checkout]',
        )
        .forEach((input) => {
            const showPicker = () => {
                if (typeof input.showPicker === "function") {
                    input.showPicker();
                }
            };
            input.addEventListener("focus", showPicker);
            input.addEventListener("click", showPicker);
        });
};

const initNavbarCollapse = () => {
    const toggler = document.querySelector(".navbar-toggler");
    const navLinks = document.querySelectorAll("#ftco-nav .nav-link");
    if (!toggler || !navLinks.length) {
        return;
    }

    navLinks.forEach((link) => {
        link.addEventListener("click", () => {
            if (toggler.offsetParent !== null) {
                toggler.click();
            }
        });
    });
};

const initPaymentOptions = () => {
    const submitButton = document.querySelector("[data-payment-submit]");
    const cardFields = document.querySelector("[data-payment-card-fields]");
    const options = document.querySelectorAll("[data-payment-option]");
    const paymentOptions = document.querySelectorAll(".booking-payment-option");

    if (!submitButton || !options.length) {
        return;
    }

    const update = (value) => {
        paymentOptions.forEach((option) => option.classList.remove("is-selected"));
        const selectedWrapper = document.querySelector(
            `[data-payment-option="${value}"]`,
        )?.closest(".booking-payment-option");
        if (selectedWrapper) {
            selectedWrapper.classList.add("is-selected");
        }
        if (value === "zalopay") {
            submitButton.textContent = "Thanh toán và hiển thị QR";
            cardFields?.setAttribute("hidden", "hidden");
        } else if (value === "card") {
            submitButton.textContent = "Thanh toán thẻ";
            cardFields?.removeAttribute("hidden");
        } else {
            submitButton.textContent = "Chọn phương thức thanh toán";
            cardFields?.setAttribute("hidden", "hidden");
        }
    };

    options.forEach((input) => {
        input.addEventListener("change", () => update(input.dataset.paymentOption));
    });

    update("");
};

document.addEventListener(
    "DOMContentLoaded",
    () => {
        applyBackgroundImages();
        enhanceBookingForm();
        initGuestPickers();
        initRoomQuantitySteppers();
        initRoomSliders();
        initRoomAmenitiesModal();
        initSearchSummaryControls();
        initRoomSelection();
        initNativeDatePickers();
        initNavbarCollapse();
        initPaymentOptions();
        window.setTimeout(hideLoader, 250);
    },
    { once: true },
);

window.addEventListener(
    "load",
    () => {
        window.setTimeout(hideLoader, 1200);
    },
    { once: true },
);

try {
    await import("./jquery-migrate-3.0.1.min.js");
    await import("./bootstrap.min.js");
    await import("./jquery.easing.1.3.js");
    await import("./jquery.waypoints.min.js");
    await import("./owl.carousel.min.js");
    await import("./jquery.magnific-popup.min.js");
    await import("./bootstrap-datepicker.js");

    await import("./main.js");
    initVietnameseDatepicker();
} catch (error) {
    console.error("Customer UI asset boot failed:", error);
    hideLoader();
}

const mapElement = document.getElementById("map");

if (mapElement) {
    const mapsScript = document.createElement("script");

    mapsScript.src =
        "https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false";
    mapsScript.async = true;
    mapsScript.onload = () => import("./customer-map.js");

    document.head.appendChild(mapsScript);
}
