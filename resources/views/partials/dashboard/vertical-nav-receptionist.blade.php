<ul class="navbar-nav iq-main-menu" id="sidebar">
    <li class="nav-item static-item">
        <a class="nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Trang Chủ</span>
            <span class="mini-icon">-</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ activeRoute(route('reception.dashboard')) }}" href="{{ route('reception.dashboard') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M4 10C4 7.79086 5.79086 6 8 6H16C18.2091 6 20 7.79086 20 10V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V10Z" fill="currentColor"></path>
                    <path d="M7 8.5C7 7.67157 7.67157 7 8.5 7H15.5C16.3284 7 17 7.67157 17 8.5V10H7V8.5Z" fill="currentColor"></path>
                    <path d="M9 13H15C15.5523 13 16 13.4477 16 14C16 14.5523 15.5523 15 15 15H9C8.44772 15 8 14.5523 8 14C8 13.4477 8.44772 13 9 13Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Thống Kê</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ activeRoute(route('reception.customers.index')) }}" href="{{ route('reception.customers.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.9488 14.54C8.49884 14.54 5.58789 15.1038 5.58789 17.2795C5.58789 19.4562 8.51765 20.0001 11.9488 20.0001C15.3988 20.0001 18.3098 19.4364 18.3098 17.2606C18.3098 15.084 15.38 14.54 11.9488 14.54Z" fill="currentColor"></path>
                    <path opacity="0.4" d="M11.949 12.467C14.2851 12.467 16.1583 10.5831 16.1583 8.23351C16.1583 5.88306 14.2851 4 11.949 4C9.61293 4 7.73975 5.88306 7.73975 8.23351C7.73975 10.5831 9.61293 12.467 11.949 12.467Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Khách Hàng</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ activeRoute(route('reception.bookings.index')) }}" href="{{ route('reception.bookings.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M6 4H18C19.1046 4 20 4.89543 20 6V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V6C4 4.89543 4.89543 4 6 4Z" fill="currentColor"></path>
                    <path d="M8 2C8.55228 2 9 2.44772 9 3V5H8C7.44772 5 7 4.55228 7 4V3C7 2.44772 7.44772 2 8 2Z" fill="currentColor"></path>
                    <path d="M16 2C16.5523 2 17 2.44772 17 3V4C17 4.55228 16.5523 5 16 5H15V3C15 2.44772 15.4477 2 16 2Z" fill="currentColor"></path>
                    <path d="M7 10H17" stroke="white" stroke-width="1.7" stroke-linecap="round"></path>
                    <path d="M7 14H13" stroke="white" stroke-width="1.7" stroke-linecap="round"></path>
                </svg>
            </i>
            <span class="item-name">Đặt Phòng</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ activeRoute(route('reception.check-ins.create')) }}" href="{{ route('reception.check-ins.create') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 4H19V20H14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M5 12H15" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M11 8L15 12L11 16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
            <span class="item-name">Nhận Phòng</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ activeRoute(route('reception.bookings.index')) }}" href="{{ route('reception.bookings.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 4H19V20H14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M5 12H15" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M9 8L5 12L9 16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
            <span class="item-name">Trả Phòng</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ activeRoute(route('reception.invoices.index')) }}" href="{{ route('reception.invoices.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M7 3H14.5858C14.851 3 15.1054 3.10536 15.2929 3.29289L19.7071 7.70711C19.8946 7.89464 20 8.149 20 8.41421V18C20 19.1046 19.1046 20 18 20H7C5.89543 20 5 19.1046 5 18V5C5 3.89543 5.89543 3 7 3Z" fill="currentColor"></path>
                    <path d="M14 3.5V7C14 8.10457 14.8954 9 16 9H19.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M8.5 12H16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                    <path d="M8.5 15.5H16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                </svg>
            </i>
            <span class="item-name">Hóa Đơn</span>
        </a>
    </li>
</ul>
