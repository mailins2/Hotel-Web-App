<ul class="navbar-nav iq-main-menu"  id="sidebar">
    <li class="nav-item static-item">
        <a class="nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Trang Chủ</span>
            <span class="mini-icon">-</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{activeRoute(route('admin.dashboard'))}}" aria-current="page" href="{{route('admin.dashboard')}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M6 11C6 9.89543 6.89543 9 8 9H9C10.1046 9 11 9.89543 11 11V18C11 19.1046 10.1046 20 9 20H8C6.89543 20 6 19.1046 6 18V11Z" fill="currentColor"></path>
                    <path opacity="0.4" d="M13 6C13 4.89543 13.8954 4 15 4H16C17.1046 4 18 4.89543 18 6V18C18 19.1046 17.1046 20 16 20H15C13.8954 20 13 19.1046 13 18V6Z" fill="currentColor"></path>
                    <path d="M3 15C3 13.8954 3.89543 13 5 13H6C7.10457 13 8 13.8954 8 15V18C8 19.1046 7.10457 20 6 20H5C3.89543 20 3 19.1046 3 18V15Z" fill="currentColor"></path>
                    <path d="M20 2C20.5523 2 21 2.44772 21 3V18C21 19.1046 20.1046 20 19 20H18V5C18 3.34315 19.3431 2 21 2H20Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Báo Cáo Thống Kê</span>
        </a>
    </li>

     <li class="nav-item static-item">
        <a class="nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Quản Lý</span>
            <span class="mini-icon">-</span>
        </a>
    </li>


      <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'accounts']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'accounts'])}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M12 2C9.238 2 7 4.238 7 7V8H6C4.895 8 4 8.895 4 10V18C4 19.105 4.895 20 6 20H18C19.105 20 20 19.105 20 18V10C20 8.895 19.105 8 18 8H17V7C17 4.238 14.762 2 12 2Z" fill="currentColor"></path>
                    <path d="M12 5C10.895 5 10 5.895 10 7V8H14V7C14 5.895 13.105 5 12 5Z" fill="currentColor"></path>
                    <path d="M12 11C10.619 11 9.5 12.119 9.5 13.5C9.5 14.476 10.059 15.32 10.875 15.73V17C10.875 17.621 11.379 18.125 12 18.125C12.621 18.125 13.125 17.621 13.125 17V15.73C13.941 15.32 14.5 14.476 14.5 13.5C14.5 12.119 13.381 11 12 11Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Tài Khoản</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'customers']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'customers'])}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.9488 14.54C8.49884 14.54 5.58789 15.1038 5.58789 17.2795C5.58789 19.4562 8.51765 20.0001 11.9488 20.0001C15.3988 20.0001 18.3098 19.4364 18.3098 17.2606C18.3098 15.084 15.38 14.54 11.9488 14.54Z" fill="currentColor"></path>
                    <path opacity="0.4" d="M11.949 12.467C14.2851 12.467 16.1583 10.5831 16.1583 8.23351C16.1583 5.88306 14.2851 4 11.949 4C9.61293 4 7.73975 5.88306 7.73975 8.23351C7.73975 10.5831 9.61293 12.467 11.949 12.467Z" fill="currentColor"></path>
                    <path opacity="0.4" d="M21.0881 9.21923C21.6925 6.84176 19.9205 4.70654 17.664 4.70654C17.4187 4.70654 17.1841 4.73356 16.9549 4.77949C16.9244 4.78669 16.8904 4.802 16.8725 4.82902C16.8519 4.86324 16.8671 4.90917 16.8895 4.93889C17.5673 5.89528 17.9568 7.0597 17.9568 8.30967C17.9568 9.50741 17.5996 10.6241 16.9728 11.5508C16.9083 11.6462 16.9656 11.775 17.0793 11.7948C17.2369 11.8227 17.3981 11.8371 17.5629 11.8416C19.2059 11.8849 20.6807 10.8213 21.0881 9.21923Z" fill="currentColor"></path>
                    <path d="M22.8094 14.817C22.5086 14.1722 21.7824 13.73 20.6783 13.513C20.1572 13.3851 18.747 13.205 17.4352 13.2293C17.4155 13.232 17.4048 13.2455 17.403 13.2545C17.4003 13.2671 17.4057 13.2887 17.4316 13.3022C18.0378 13.6039 20.3811 14.916 20.0865 17.6834C20.074 17.8032 20.1698 17.9068 20.2888 17.8888C20.8655 17.8059 22.3492 17.4853 22.8094 16.4866C23.0637 15.9589 23.0637 15.3456 22.8094 14.817Z" fill="currentColor"></path>
                    <path opacity="0.4" d="M7.04459 4.77973C6.81626 4.7329 6.58077 4.70679 6.33543 4.70679C4.07901 4.70679 2.30701 6.84201 2.9123 9.21947C3.31882 10.8216 4.79355 11.8851 6.43661 11.8419C6.60136 11.8374 6.76343 11.8221 6.92013 11.7951C7.03384 11.7753 7.09115 11.6465 7.02668 11.551C6.3999 10.6234 6.04263 9.50765 6.04263 8.30991C6.04263 7.05904 6.43303 5.89462 7.11085 4.93913C7.13234 4.90941 7.14845 4.86348 7.12696 4.82926C7.10906 4.80135 7.07593 4.78694 7.04459 4.77973Z" fill="currentColor"></path>
                    <path d="M3.32156 13.5127C2.21752 13.7297 1.49225 14.1719 1.19139 14.8167C0.936203 15.3453 0.936203 15.9586 1.19139 16.4872C1.65163 17.4851 3.13531 17.8066 3.71195 17.8885C3.83104 17.9065 3.92595 17.8038 3.91342 17.6832C3.61883 14.9167 5.9621 13.6046 6.56918 13.3029C6.59425 13.2885 6.59962 13.2677 6.59694 13.2542C6.59515 13.2452 6.5853 13.2317 6.5656 13.2299C5.25294 13.2047 3.84358 13.3848 3.32156 13.5127Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Khách Hàng</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'employees']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'employees'])}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M16 13C18.7614 13 21 15.0147 21 17.5V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V17.5C3 15.0147 5.23858 13 8 13H16Z" fill="currentColor"></path>
                    <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" fill="currentColor"></path>
                    <path d="M18.5 6C19.8807 6 21 7.11929 21 8.5C21 9.88071 19.8807 11 18.5 11C17.1193 11 16 9.88071 16 8.5C16 7.11929 17.1193 6 18.5 6Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Nhân Viên</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'room-types']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'room-types'])}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M5 5C5 4.44772 5.44772 4 6 4H11C11.5523 4 12 4.44772 12 5V10C12 10.5523 11.5523 11 11 11H6C5.44772 11 5 10.5523 5 10V5Z" fill="currentColor"></path>
                    <path opacity="0.4" d="M12 14C12 13.4477 12.4477 13 13 13H18C18.5523 13 19 13.4477 19 14V19C19 19.5523 18.5523 20 18 20H13C12.4477 20 12 19.5523 12 19V14Z" fill="currentColor"></path>
                    <path d="M14 5C14 4.44772 14.4477 4 15 4H18C18.5523 4 19 4.44772 19 5V8C19 8.55228 18.5523 9 18 9H15C14.4477 9 14 8.55228 14 8V5Z" fill="currentColor"></path>
                    <path d="M5 16C5 15.4477 5.44772 15 6 15H9C9.55228 15 10 15.4477 10 16V19C10 19.5523 9.55228 20 9 20H6C5.44772 20 5 19.5523 5 19V16Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Loại Phòng</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'rooms']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'rooms'])}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M4 10C4 7.79086 5.79086 6 8 6H16C18.2091 6 20 7.79086 20 10V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V10Z" fill="currentColor"></path>
                    <path d="M7 8.5C7 7.67157 7.67157 7 8.5 7H15.5C16.3284 7 17 7.67157 17 8.5V10H7V8.5Z" fill="currentColor"></path>
                    <path d="M9 13H15C15.5523 13 16 13.4477 16 14C16 14.5523 15.5523 15 15 15H9C8.44772 15 8 14.5523 8 14C8 13.4477 8.44772 13 9 13Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Phòng</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'services']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'services'])}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M5 7C5 5.89543 5.89543 5 7 5H17C18.1046 5 19 5.89543 19 7V16C19 17.1046 18.1046 18 17 18H7C5.89543 18 5 17.1046 5 16V7Z" fill="currentColor"></path>
                    <path d="M9 3C9.55228 3 10 3.44772 10 4V6H8V4C8 3.44772 8.44772 3 9 3Z" fill="currentColor"></path>
                    <path d="M15 3C15.5523 3 16 3.44772 16 4V6H14V4C14 3.44772 14.4477 3 15 3Z" fill="currentColor"></path>
                    <path d="M10 10H14C14.5523 10 15 10.4477 15 11C15 11.5523 14.5523 12 14 12H10C9.44772 12 9 11.5523 9 11C9 10.4477 9.44772 10 10 10Z" fill="currentColor"></path>
                    <path d="M8 20C8 18.8954 8.89543 18 10 18H14C15.1046 18 16 18.8954 16 20V21H8V20Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Dịch Vụ</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'promotions']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'promotions'])}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M4 8C4 6.89543 4.89543 6 6 6H12.5C13.0304 6 13.5391 6.21071 13.9142 6.58579L15.4142 8.08579C15.7893 8.46086 16.298 8.67157 16.8284 8.67157H18C19.1046 8.67157 20 9.56699 20 10.6716V17C20 18.1046 19.1046 19 18 19H6C4.89543 19 4 18.1046 4 17V8Z" fill="currentColor"></path>
                    <path d="M13.5 4C14.0523 4 14.5 4.44772 14.5 5V7.5C14.5 8.05228 14.0523 8.5 13.5 8.5H11C10.4477 8.5 10 8.05228 10 7.5C10 6.94772 10.4477 6.5 11 6.5H12.5V5C12.5 4.44772 12.9477 4 13.5 4Z" fill="currentColor"></path>
                    <path d="M9.5 11.5C10.0523 11.5 10.5 11.9477 10.5 12.5V13.5H11.5C12.0523 13.5 12.5 13.9477 12.5 14.5C12.5 15.0523 12.0523 15.5 11.5 15.5H10.5V16.5C10.5 17.0523 10.0523 17.5 9.5 17.5C8.94772 17.5 8.5 17.0523 8.5 16.5V15.5H7.5C6.94772 15.5 6.5 15.0523 6.5 14.5C6.5 13.9477 6.94772 13.5 7.5 13.5H8.5V12.5C8.5 11.9477 8.94772 11.5 9.5 11.5Z" fill="currentColor"></path>
                    <path d="M15.5 13C16.3284 13 17 13.6716 17 14.5C17 15.3284 16.3284 16 15.5 16C14.6716 16 14 15.3284 14 14.5C14 13.6716 14.6716 13 15.5 13Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Khuyến Mãi</span>
        </a>
    </li>

      <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'invoices']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'invoices'])}}">
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

    <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'payments']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'payments'])}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M4 7C4 5.89543 4.89543 5 6 5H18C19.1046 5 20 5.89543 20 7V17C20 18.1046 19.1046 19 18 19H6C4.89543 19 4 18.1046 4 17V7Z" fill="currentColor"></path>
                    <path d="M4 9H20V11H4V9Z" fill="currentColor"></path>
                    <path d="M8 13.5C8 12.9477 8.44772 12.5 9 12.5H11C11.5523 12.5 12 12.9477 12 13.5C12 14.0523 11.5523 14.5 11 14.5H9C8.44772 14.5 8 14.0523 8 13.5Z" fill="currentColor"></path>
                    <path d="M14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5C19 16.8807 17.8807 18 16.5 18C15.1193 18 14 16.8807 14 15.5Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Thanh Toán</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{activeRoute(route('hotel.modules.index', ['moduleKey' => 'reviews']))}}" href="{{route('hotel.modules.index', ['moduleKey' => 'reviews'])}}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M12 4L14.4721 9.00872L20 9.8123L16 13.7106L16.9443 19.2154L12 16.6154L7.05573 19.2154L8 13.7106L4 9.8123L9.52786 9.00872L12 4Z" fill="currentColor"></path>
                    <path d="M12 7.5L13.2361 10.0044C13.3816 10.2994 13.6631 10.5039 13.9889 10.5513L16.7526 10.9529L14.7526 12.9024C14.5168 13.1323 14.4091 13.4635 14.4648 13.788L14.9365 16.5401L12.4648 15.2401C12.1738 15.087 11.8262 15.087 11.5352 15.2401L9.06353 16.5401L9.53519 13.788C9.59085 13.4635 9.4832 13.1323 9.24736 12.9024L7.24736 10.9529L10.0111 10.5513C10.3369 10.5039 10.6184 10.2994 10.7639 10.0044L12 7.5Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Đánh Giá</span>
        </a>
    </li>
</ul>
