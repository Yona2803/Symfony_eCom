function Unnecessary(event) {
  event.stopPropagation();
}

function LoadingHTML(Caller) {
  const LoadingHTML = `<div class="LoadingOrder">
							<div class="iconWait ${
                Caller == "Orders"
                  ? "OrderColor"
                  : Caller == "Returns"
                  ? "ReturnColor"
                  : "CancelColor"
              }"></div>
							<div>
								<svg class="LoadingOrderSvg" width="96" height="14" viewbox="0 0 96 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M7 7H89" stroke="#D5D5D5" stroke-width="14" stroke-linecap="round"/>
								</svg>
								<svg class="LoadingOrderSvg" width="90" height="14" viewbox="0 0 90 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M7 7H83" stroke="#D5D5D5" stroke-width="14" stroke-linecap="round"/>
								</svg>
								<svg class="LoadingOrderSvg" width="145" height="14" viewbox="0 0 145 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M7 7H138" stroke="#D5D5D5" stroke-width="14" stroke-linecap="round"/>
								</svg>
							</div>
							<div>
								<svg class="LoadingOrderSvg" width="78" height="14" viewbox="0 0 78 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M7 7H71" stroke="#D5D5D5" stroke-width="14" stroke-linecap="round"/>
								</svg>
							</div>
						</div>`;

  return LoadingHTML;
}

async function FetchRecordsBy(Caller) {
  LoadingElements(true);
  dataCache = {};
  let Orders_List = document.querySelector(".Orders_List");
  Orders_List.innerHTML = LoadingHTML(Caller);

  // Sending by Ajax to Back-End => Show response in HTML
  $.ajax({
    url: `/Orders/FetchRecordsby/${Caller}`,
    type: "GET",
    success: function (response) {
      Orders_List.innerHTML = "";
      let OrderHTML = "";
      if (response && response.length > 0) {
        response.forEach(function (Order) {
          OrderHTML = `<div class="Order ThisOrder" id="Order${
            Order.id
          }" onclick="fetchWithCacheCondition(${Order.id})">
                        <div class="Top-Section">
                            <div class="Icon UnDetailed_TS_Icon ${
                              Caller == "Orders"
                                ? "OrderColor"
                                : Caller == "Returns"
                                ? "ReturnColor"
                                : "CancelColor"
                            }">
                                <svg class="BoxClose" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path 
                                        d="M9.90366 0.0580311C9.4987 0.101091 0.470575 3.35209 0.172813 3.56739C-0.0653959 3.72886 -0.0534855 15.4626 0.184724 15.947C0.542038 16.7113 0.708784 16.7867 7.98608 19.4133C9.67736 20.0269 9.71309 20.0269 10.6659 19.9839C11.583 19.93 11.857 19.8439 15.7993 18.4014C20.0871 16.8297 20.4921 16.6252 20.8136 15.947C21.0519 15.4626 21.0638 3.72886 20.8375 3.56739C20.7541 3.51356 20.0633 3.24444 19.3129 2.96455C18.5626 2.69543 16.6092 1.97418 14.9656 1.37135C12.3572 0.413273 10.8446 -0.0496178 10.4396 0.00420761C10.3801 0.0149727 10.13 0.0365028 9.90366 0.0580311ZM11.8093 1.13452L12.3215 1.32829L11.0828 1.78041C10.4039 2.02801 8.45058 2.73849 6.74739 3.36285C5.04419 3.98722 3.60303 4.50393 3.54347 4.50393C3.29335 4.50393 1.81646 3.91187 1.88792 3.84727C1.99511 3.76116 9.45106 1.01611 9.84411 0.919224C10.261 0.811575 11.2853 0.929989 11.8093 1.13452ZM14.2152 2.01724C14.5011 2.12489 14.7989 2.32942 14.8822 2.4586C15.0132 2.65237 15.1204 2.68467 15.3825 2.63084C15.7279 2.57702 16.5735 2.81384 16.383 2.91073C16.3234 2.94302 15.2038 3.36285 13.8937 3.83651C12.5835 4.31017 10.6659 5.02065 9.62972 5.40819L7.74787 6.1079L7.2119 5.8926C6.91413 5.78496 6.56873 5.58042 6.44963 5.45124C6.29479 5.26824 6.12804 5.21442 5.78264 5.23595C5.52061 5.25748 5.21094 5.19289 5.09183 5.11753C4.90127 4.98835 5.11565 4.8807 7.23572 4.09487C12.2858 2.23254 13.4172 1.82347 13.5602 1.81271C13.6316 1.81271 13.9294 1.90959 14.2152 2.01724ZM18.7174 3.66427L19.2534 3.87957L18.5387 4.13793C18.1457 4.28864 16.1686 5.00988 14.1557 5.75266L10.4873 7.09827L9.78456 6.85068C9.39151 6.71074 9.06993 6.58156 9.06993 6.54926C9.06993 6.51697 11.0113 5.78496 13.3934 4.913C16.1567 3.9011 17.7884 3.35209 17.9432 3.39515C18.0742 3.42744 18.4196 3.55662 18.7174 3.66427ZM2.60255 5.11753L3.05514 5.3113L3.1147 7.41046L3.17425 9.50961L3.76977 9.63879C4.32956 9.76797 4.67497 9.73567 5.25858 9.52037C5.40151 9.46655 5.48488 9.55267 5.60398 9.83256C5.78264 10.2739 6.28288 10.6507 7.1047 10.9413C7.70022 11.1459 7.98608 11.1459 8.14091 10.9198C8.18855 10.8552 8.2362 9.99403 8.2362 8.9929C8.2362 8.00252 8.24811 7.19516 8.27193 7.19516C8.28384 7.19516 8.67688 7.3351 9.12948 7.50734L9.96321 7.80876L9.99894 13.4711C10.0228 18.2076 9.99894 19.1442 9.85602 19.1442C9.77264 19.1442 7.85506 18.466 5.60398 17.6371C2.67401 16.5606 1.43532 16.0547 1.24475 15.8609L0.970814 15.581V10.0479V4.50393L1.55443 4.71923C1.87601 4.82688 2.35243 5.00988 2.60255 5.11753ZM19.8132 15.8071C19.6464 16.0116 18.5745 16.453 15.6088 17.551C13.4172 18.3584 11.4878 19.0581 11.2972 19.0904L10.9756 19.1657L10.9994 13.4819L11.0352 7.80876L15.2634 6.25861C17.5859 5.39742 19.5988 4.66541 19.7298 4.62235C19.968 4.53623 19.968 4.57929 20.0037 10.0371C20.0276 15.2365 20.0156 15.5487 19.8132 15.8071ZM5.69927 6.24785L7.28336 6.81839V8.46542V10.1232L6.90222 9.91868C6.61637 9.76797 6.46154 9.5742 6.33052 9.20819C6.05658 8.43312 5.61589 8.29318 4.79407 8.72377C4.00798 9.13284 3.94843 9.03595 3.94843 7.24898C3.94843 6.38779 3.98416 5.68807 4.04371 5.68807C4.09135 5.68807 4.84171 5.93567 5.69927 6.24785Z"
                                        fill="#000" />
                                </svg>
                                <svg class="BoxOpen" width="48" height="46" viewBox="0 0 48 46" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M16.299 3.85009C12.2404 5.65868 8.02737 7.53962 6.94655 7.9978L4.98343 8.84181L2.60121 12.3384C1.23364 14.364 0.152819 16.1726 0.0645892 16.6067C-0.244217 18.3912 0.527798 19.4281 2.95413 20.4409L4.60845 21.1402V28.5675C4.60845 36.6459 4.65257 36.9594 5.86573 38.1893C6.41717 38.7921 15.2181 42.8675 20.7767 45.1584C23.4456 46.2435 24.9455 46.1471 28.188 44.7484C37.386 40.7213 41.6211 38.768 42.0843 38.3098C42.8783 37.5141 43.4298 35.9948 43.4298 34.5721C43.4298 30.3771 43.4298 25.3845 43.4298 22.0084C43.4298 21.1644 43.4518 21.1403 45.04 20.4409C45.9002 20.0551 46.8046 19.5728 47.0472 19.3558C47.731 18.7047 48.106 17.6436 47.9736 16.7755C47.8413 16.0762 44.7974 11.205 43.3857 9.42056C43.0548 9.03473 41.7534 8.31129 39.7903 7.46727C38.0919 6.71972 33.8789 4.83879 30.4158 3.31957C26.9528 1.77623 24.0412 0.522278 23.9088 0.546391C23.7985 0.546391 20.3576 2.04149 16.299 3.85009ZM23.1368 9.68582C23.1368 13.3995 23.0706 16.4379 23.0045 16.4379C22.8942 16.4379 10.4978 10.988 8.57881 10.0958L7.8068 9.73405L8.35824 9.46879C9.52729 8.89004 22.806 2.98196 22.9824 2.95785C23.0706 2.93373 23.1368 5.97217 23.1368 9.68582ZM32.0701 6.06863C35.7096 7.68431 39.0403 9.17941 39.4594 9.37233L40.2314 9.70993L37.9154 10.7469C29.3129 14.6052 25.1441 16.4379 25.0338 16.4379C24.9676 16.4379 24.9014 13.3995 24.9014 9.66171C24.9014 4.33238 24.9676 2.90962 25.1882 3.00608C25.3205 3.07842 28.4306 4.45295 32.0701 6.06863ZM14.9755 14.991C18.9238 16.7514 22.2325 18.2706 22.3427 18.3671C22.5854 18.6323 18.4827 24.7333 17.9753 24.8298C17.6445 24.9021 15.1961 23.8652 5.51281 19.5246C3.63792 18.6806 2.00566 17.8124 1.87331 17.5954C1.58656 17.089 1.65273 16.9202 3.99084 13.4477L5.71133 10.8915L6.77009 11.3497C7.34359 11.5909 11.0493 13.2307 14.9755 14.991ZM44.29 13.8818C45.3929 15.5216 46.2973 17.0408 46.2973 17.2337C46.2973 17.4266 46.1649 17.6919 46.0326 17.8366C45.5032 18.2947 30.3717 24.9021 30.0188 24.8298C29.4232 24.7092 25.3867 18.7047 25.6955 18.3671C25.8058 18.2465 28.9821 16.7755 32.7319 15.1116C36.5037 13.4477 40.1212 11.832 40.7829 11.4944C41.4446 11.1809 42.0622 10.9157 42.1284 10.9157C42.2166 10.8915 43.1872 12.242 44.29 13.8818ZM23.1368 32.4982V43.9768L22.5413 43.808C21.8575 43.6392 8.55676 37.7793 7.58622 37.2247C7.2333 37.0318 6.81421 36.6942 6.6598 36.4771C6.26277 35.9225 6.26277 21.8878 6.6598 22.0566C6.79215 22.1289 8.75527 22.9971 11.0051 23.9858C13.255 24.9745 15.5931 26.0355 16.1887 26.3249C17.5121 26.9519 18.7474 26.8554 19.4973 26.0837C19.7841 25.7944 20.6664 24.5404 21.5046 23.2864C22.3207 22.0325 23.0265 21.0197 23.0706 21.0197C23.1148 21.0197 23.1368 26.1802 23.1368 32.4982ZM28.7615 26.2767C29.0924 26.566 29.6659 26.8072 29.9967 26.8072C30.3496 26.8072 33.0848 25.722 36.0626 24.3957C39.0624 23.0694 41.5549 21.9843 41.577 21.9843C41.6211 21.9843 41.6652 25.1192 41.6652 28.9534C41.6652 37.3453 41.974 36.5977 37.9154 38.4063C31.3643 41.3242 25.3426 43.9285 25.1441 43.9285C24.9676 43.9285 24.9014 40.3596 24.9014 32.4259V20.9473L26.5116 23.3347C27.416 24.661 28.4086 25.9873 28.7615 26.2767Z"
                                        fill="white" />
                                </svg>
                            </div>
                            <div class="Top-Section-left UnDetailed_TS_Left">
                                <h4 >Order&nbsp;<span>#${Order.id}</span></h4>
                                <div>
                                    <p><span class="dot"></span> Items :&nbsp; <span>${
                                      Order.detailsCount
                                    }</span></p>
                                    <p><span class="dot"></span> Amount :&nbsp; <span>${new Intl.NumberFormat(
                                      "fr-FR",
                                      {
                                        style: "decimal",
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2,
                                      }
                                    )
                                      .format(Order.totalAmount)
                                      .replace(",", ".")}&nbsp;Dh</span></p>
                                    <p>
                                    <span class="dot"></span> <span id="StatusId${
                                      Order.id
                                    }">${
            Caller == "Orders"
              ? Order.statusName
              : Order.statusName !== "Cancelled" &&
                Order.statusName !== "Returned" &&
                Order.state !== "Return" &&
                Order.state !== "Cancel"
              ? Order.statusName
              : Order.stateStatus
          }</span></p>
                                </div>
                            </div>
                            <div class="Top-Section-Right Switch_TS_Right">
                                <p>${new Date(
                                  Order.orderDate.date
                                ).toLocaleDateString("fr-FR", {
                                  day: "2-digit",
                                  month: "2-digit",
                                  year: "numeric",
                                })}</p>
            
                                ${
                                  (Order.statusName !== "Cancelled" &&
                                  Order.statusName !== "Returned" &&
                                  Order.state !== "Return" &&
                                  Order.state !== "Cancel") ||  (Order.stateStatus === "Declined" && Caller == "Orders")
                                    ? ` 
                                <div class="dropdown" id="MenuOf${Order.id}">
                                    <button class="dropbtn">Change Status
                                        <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4 6.5L4 1M7 4.5L4 7.5L1 4.5" stroke="rgb(24, 74, 72, 0.5)"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <div class="dropdown-content">
                                    ${
                                      Order.statusName == "Delivered"
                                        ? `
                                        <button id="Returned/${Order.id}"><svg width="15" height="13" viewBox="0 0 15 13" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M5.65923 0.0319185C5.42783 0.0556011 0.2689 1.84365 0.0987504 1.96206C-0.0373691 2.05087 -0.0305631 8.50443 0.105556 8.77086C0.309736 9.19123 0.40502 9.23267 4.56347 10.6773C5.52992 11.0148 5.55034 11.0148 6.09482 10.9911C6.61888 10.9615 6.77541 10.9142 9.02819 10.1208C11.4783 9.25636 11.7097 9.14386 11.8935 8.77086C12.0296 8.50443 12.0364 2.05087 11.9071 1.96206C11.8595 1.93246 11.4647 1.78444 11.036 1.63051C10.6072 1.48249 9.491 1.0858 8.55177 0.754243C7.06126 0.227301 6.19691 -0.0272894 5.9655 0.00231457C5.93147 0.00823498 5.78855 0.0200768 5.65923 0.0319185ZM6.74819 0.623987L7.04085 0.73056L6.33303 0.979229C5.94509 1.1154 4.82891 1.50617 3.85565 1.84957C2.8824 2.19297 2.05887 2.47716 2.02484 2.47716C1.88192 2.47716 1.03798 2.15153 1.07881 2.116C1.14006 2.06864 5.40061 0.55886 5.6252 0.505574C5.86341 0.446367 6.44873 0.511494 6.74819 0.623987ZM8.123 1.10948C8.28634 1.16869 8.45649 1.28118 8.50413 1.35223C8.579 1.45881 8.64025 1.47657 8.78998 1.44696C8.98736 1.41736 9.47058 1.54762 9.36169 1.6009C9.32766 1.61866 8.68789 1.84957 7.93924 2.11008C7.19058 2.37059 6.09482 2.76136 5.5027 2.9745L4.42735 3.35935L4.12108 3.24093C3.95093 3.18173 3.75356 3.06923 3.6855 2.99819C3.59702 2.89753 3.50174 2.86793 3.30437 2.87977C3.15463 2.89161 2.97768 2.85609 2.90962 2.81464C2.80072 2.7436 2.92323 2.68439 4.1347 2.25218C7.02043 1.2279 7.667 1.00291 7.74867 0.996991C7.78951 0.996991 7.95965 1.05028 8.123 1.10948ZM10.6957 2.01535L11.0019 2.13376L10.5936 2.27586C10.369 2.35875 9.23918 2.75544 8.08897 3.16396L5.99273 3.90405L5.59117 3.76787C5.36658 3.69091 5.18282 3.61986 5.18282 3.6021C5.18282 3.58433 6.29219 3.18173 7.65339 2.70215C9.23237 2.14561 10.1648 1.84365 10.2533 1.86733C10.3281 1.88509 10.5255 1.95614 10.6957 2.01535ZM1.48717 2.81464L1.7458 2.92122L1.77983 4.07575L1.81386 5.23029L2.15416 5.30133C2.47404 5.37238 2.67141 5.35462 3.0049 5.23621C3.08657 5.2066 3.13422 5.25397 3.20228 5.40791C3.30437 5.65065 3.59022 5.85788 4.05983 6.01774C4.40013 6.13023 4.56347 6.13023 4.65195 6.0059C4.67917 5.97037 4.7064 5.49672 4.7064 4.94609C4.7064 4.40139 4.7132 3.95734 4.72682 3.95734C4.73362 3.95734 4.95822 4.03431 5.21685 4.12904L5.69326 4.29482L5.71368 7.4091C5.72729 10.0142 5.71368 10.5293 5.63201 10.5293C5.58437 10.5293 4.48861 10.1563 3.20228 9.70041C1.52801 9.10834 0.820184 8.83007 0.711288 8.72349L0.554751 8.56956V5.52632V2.47716L0.888244 2.59558C1.07201 2.65478 1.34424 2.75544 1.48717 2.81464ZM11.3218 8.69389C11.2265 8.80638 10.614 9.04913 8.9193 9.65304C7.667 10.0971 6.56443 10.4819 6.45553 10.4997L6.27177 10.5411L6.28538 7.41502L6.3058 4.29482L8.72192 3.44224C10.0491 2.96858 11.1993 2.56597 11.2742 2.54229C11.4103 2.49493 11.4796 1.9443 11.5 4.94609C11.5136 7.80579 11.4375 8.55179 11.3218 8.69389ZM3.25672 3.43632L4.16192 3.75011V4.65598V5.56777L3.94413 5.45527C3.78078 5.37238 3.69231 5.26581 3.61744 5.06451C3.4609 4.63822 3.20908 4.56125 2.73947 4.79808C2.29028 5.02306 2.25625 4.96978 2.25625 3.98694C2.25625 3.51329 2.27666 3.12844 2.31069 3.12844C2.33792 3.12844 2.76669 3.26462 3.25672 3.43632Z"
                                                    fill="white" />
                                                <circle cx="11" cy="9" r="4" fill="white" />
                                                <path d="M9.61538 9L13 9M10.8462 11L9 9L10.8462 7" stroke="#4F7473"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Return it</button>
                                         `
                                        : ``
                                    }   
                                    ${
                                      Order.statusName !== "Shipped"&&
                                      Order.statusName !== "Delivered" 
                                        ? `
                                        <button id="Cancelled/${Order.id}"><svg width="15" height="13" viewBox="0 0 15 13" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M5.65923 0.0319185C5.42783 0.0556011 0.2689 1.84365 0.0987504 1.96206C-0.0373691 2.05087 -0.0305631 8.50443 0.105556 8.77086C0.309736 9.19123 0.40502 9.23267 4.56347 10.6773C5.52992 11.0148 5.55034 11.0148 6.09482 10.9911C6.61888 10.9615 6.77541 10.9142 9.02819 10.1208C11.4783 9.25636 11.7097 9.14386 11.8935 8.77086C12.0296 8.50443 12.0364 2.05087 11.9071 1.96206C11.8595 1.93246 11.4647 1.78444 11.036 1.63051C10.6072 1.48249 9.491 1.0858 8.55177 0.754243C7.06126 0.227301 6.19691 -0.0272894 5.9655 0.00231457C5.93147 0.00823498 5.78855 0.0200768 5.65923 0.0319185ZM6.74819 0.623987L7.04085 0.73056L6.33303 0.979229C5.94509 1.1154 4.82891 1.50617 3.85565 1.84957C2.8824 2.19297 2.05887 2.47716 2.02484 2.47716C1.88192 2.47716 1.03798 2.15153 1.07881 2.116C1.14006 2.06864 5.40061 0.55886 5.6252 0.505574C5.86341 0.446367 6.44873 0.511494 6.74819 0.623987ZM8.123 1.10948C8.28634 1.16869 8.45649 1.28118 8.50413 1.35223C8.579 1.45881 8.64025 1.47657 8.78998 1.44696C8.98736 1.41736 9.47058 1.54762 9.36169 1.6009C9.32766 1.61866 8.68789 1.84957 7.93924 2.11008C7.19058 2.37059 6.09482 2.76136 5.5027 2.9745L4.42735 3.35935L4.12108 3.24093C3.95093 3.18173 3.75356 3.06923 3.6855 2.99819C3.59702 2.89753 3.50174 2.86793 3.30437 2.87977C3.15463 2.89161 2.97768 2.85609 2.90962 2.81464C2.80072 2.7436 2.92323 2.68439 4.1347 2.25218C7.02043 1.2279 7.667 1.00291 7.74867 0.996991C7.78951 0.996991 7.95965 1.05028 8.123 1.10948ZM10.6957 2.01535L11.0019 2.13376L10.5936 2.27586C10.369 2.35875 9.23918 2.75544 8.08897 3.16396L5.99273 3.90405L5.59117 3.76787C5.36658 3.69091 5.18282 3.61986 5.18282 3.6021C5.18282 3.58433 6.29219 3.18173 7.65339 2.70215C9.23237 2.14561 10.1648 1.84365 10.2533 1.86733C10.3281 1.88509 10.5255 1.95614 10.6957 2.01535ZM1.48717 2.81464L1.7458 2.92122L1.77983 4.07575L1.81386 5.23029L2.15416 5.30133C2.47404 5.37238 2.67141 5.35462 3.0049 5.23621C3.08657 5.2066 3.13422 5.25397 3.20228 5.40791C3.30437 5.65065 3.59022 5.85788 4.05983 6.01774C4.40013 6.13023 4.56347 6.13023 4.65195 6.0059C4.67917 5.97037 4.7064 5.49672 4.7064 4.94609C4.7064 4.40139 4.7132 3.95734 4.72682 3.95734C4.73362 3.95734 4.95822 4.03431 5.21685 4.12904L5.69326 4.29482L5.71368 7.4091C5.72729 10.0142 5.71368 10.5293 5.63201 10.5293C5.58437 10.5293 4.48861 10.1563 3.20228 9.70041C1.52801 9.10834 0.820184 8.83007 0.711288 8.72349L0.554751 8.56956V5.52632V2.47716L0.888244 2.59558C1.07201 2.65478 1.34424 2.75544 1.48717 2.81464ZM11.3218 8.69389C11.2265 8.80638 10.614 9.04913 8.9193 9.65304C7.667 10.0971 6.56443 10.4819 6.45553 10.4997L6.27177 10.5411L6.28538 7.41502L6.3058 4.29482L8.72192 3.44224C10.0491 2.96858 11.1993 2.56597 11.2742 2.54229C11.4103 2.49493 11.4796 1.9443 11.5 4.94609C11.5136 7.80579 11.4375 8.55179 11.3218 8.69389ZM3.25672 3.43632L4.16192 3.75011V4.65598V5.56777L3.94413 5.45527C3.78078 5.37238 3.69231 5.26581 3.61744 5.06451C3.4609 4.63822 3.20908 4.56125 2.73947 4.79808C2.29028 5.02306 2.25625 4.96978 2.25625 3.98694C2.25625 3.51329 2.27666 3.12844 2.31069 3.12844C2.33792 3.12844 2.76669 3.26462 3.25672 3.43632Z"
                                                    fill="white" />
                                                <circle cx="11" cy="9" r="4" fill="white" />
                                                <path d="M9 7L11 9M13 11L11 9M11 9L13 7L9 11" stroke="#4F7473"
                                                    stroke-linecap="round" />
                                            </svg>
                                            Cancel it</button>
                                       `
                                        : ``
                                    }   
                                    ${Order.statusName !== "Delivered" ? `
                                        <button id="Delivered/${
                                          Order.id
                                        }"><svg width="15" height="13" viewBox="0 0 15 13" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M5.65923 0.0319185C5.42783 0.0556011 0.2689 1.84365 0.0987504 1.96206C-0.0373691 2.05087 -0.0305631 8.50443 0.105556 8.77086C0.309736 9.19123 0.40502 9.23267 4.56347 10.6773C5.52992 11.0148 5.55034 11.0148 6.09482 10.9911C6.61888 10.9615 6.77541 10.9142 9.02819 10.1208C11.4783 9.25636 11.7097 9.14386 11.8935 8.77086C12.0296 8.50443 12.0364 2.05087 11.9071 1.96206C11.8595 1.93246 11.4647 1.78444 11.036 1.63051C10.6072 1.48249 9.491 1.0858 8.55177 0.754243C7.06126 0.227301 6.19691 -0.0272894 5.9655 0.00231457C5.93147 0.00823498 5.78855 0.0200768 5.65923 0.0319185ZM6.74819 0.623987L7.04085 0.73056L6.33303 0.979229C5.94509 1.1154 4.82891 1.50617 3.85565 1.84957C2.8824 2.19297 2.05887 2.47716 2.02484 2.47716C1.88192 2.47716 1.03798 2.15153 1.07881 2.116C1.14006 2.06864 5.40061 0.55886 5.6252 0.505574C5.86341 0.446367 6.44873 0.511494 6.74819 0.623987ZM8.123 1.10948C8.28634 1.16869 8.45649 1.28118 8.50413 1.35223C8.579 1.45881 8.64025 1.47657 8.78998 1.44696C8.98736 1.41736 9.47058 1.54762 9.36169 1.6009C9.32766 1.61866 8.68789 1.84957 7.93924 2.11008C7.19058 2.37059 6.09482 2.76136 5.5027 2.9745L4.42735 3.35935L4.12108 3.24093C3.95093 3.18173 3.75356 3.06923 3.6855 2.99819C3.59702 2.89753 3.50174 2.86793 3.30437 2.87977C3.15463 2.89161 2.97768 2.85609 2.90962 2.81464C2.80072 2.7436 2.92323 2.68439 4.1347 2.25218C7.02043 1.2279 7.667 1.00291 7.74867 0.996991C7.78951 0.996991 7.95965 1.05028 8.123 1.10948ZM10.6957 2.01535L11.0019 2.13376L10.5936 2.27586C10.369 2.35875 9.23918 2.75544 8.08897 3.16396L5.99273 3.90405L5.59117 3.76787C5.36658 3.69091 5.18282 3.61986 5.18282 3.6021C5.18282 3.58433 6.29219 3.18173 7.65339 2.70215C9.23237 2.14561 10.1648 1.84365 10.2533 1.86733C10.3281 1.88509 10.5255 1.95614 10.6957 2.01535ZM1.48717 2.81464L1.7458 2.92122L1.77983 4.07575L1.81386 5.23029L2.15416 5.30133C2.47404 5.37238 2.67141 5.35462 3.0049 5.23621C3.08657 5.2066 3.13422 5.25397 3.20228 5.40791C3.30437 5.65065 3.59022 5.85788 4.05983 6.01774C4.40013 6.13023 4.56347 6.13023 4.65195 6.0059C4.67917 5.97037 4.7064 5.49672 4.7064 4.94609C4.7064 4.40139 4.7132 3.95734 4.72682 3.95734C4.73362 3.95734 4.95822 4.03431 5.21685 4.12904L5.69326 4.29482L5.71368 7.4091C5.72729 10.0142 5.71368 10.5293 5.63201 10.5293C5.58437 10.5293 4.48861 10.1563 3.20228 9.70041C1.52801 9.10834 0.820184 8.83007 0.711288 8.72349L0.554751 8.56956V5.52632V2.47716L0.888244 2.59558C1.07201 2.65478 1.34424 2.75544 1.48717 2.81464ZM11.3218 8.69389C11.2265 8.80638 10.614 9.04913 8.9193 9.65304C7.667 10.0971 6.56443 10.4819 6.45553 10.4997L6.27177 10.5411L6.28538 7.41502L6.3058 4.29482L8.72192 3.44224C10.0491 2.96858 11.1993 2.56597 11.2742 2.54229C11.4103 2.49493 11.4796 1.9443 11.5 4.94609C11.5136 7.80579 11.4375 8.55179 11.3218 8.69389ZM3.25672 3.43632L4.16192 3.75011V4.65598V5.56777L3.94413 5.45527C3.78078 5.37238 3.69231 5.26581 3.61744 5.06451C3.4609 4.63822 3.20908 4.56125 2.73947 4.79808C2.29028 5.02306 2.25625 4.96978 2.25625 3.98694C2.25625 3.51329 2.27666 3.12844 2.31069 3.12844C2.33792 3.12844 2.76669 3.26462 3.25672 3.43632Z"
                                                    fill="white" />
                                                <circle cx="11" cy="9" r="4" fill="white" />
                                                <path d="M9 9.5L10.3333 11L13 8" stroke="#4F7473" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            Delivered</button>
                                                                                     ` :``}   
                                    </div>
                                </div>
                                `
                                    : ``
                                }
                            </div>
                        </div>
                        ${
                          Caller !== "Orders"
                            ? `<div
                              class="Middle-Section StatusCancel"
                              onclick="Unnecessary(event)"
                            >
                              <p class="Middle-Section-Msg" id="BS-Return${
                                Order.id
                              }-Msg">
                                <span class="dot"></span>${
                                  Order.stateStatus === `Pending`
                                    ? `Your ${Order.state} request is under review...<span>&nbsp;⏳&nbsp;</span>`
                                    : Order.stateStatus === `Declined`
                                    ? `Your ${Order.state} request has been declined <span>&nbsp;❌&nbsp;</span> for more information check your email`
                                    : `Your ${Order.state} request has been accepted <span>&nbsp;✔&nbsp;</span> for more information check your email`
                                }
                                </p>
                            </div>`
                            : ``
                        } 
                       <div class="Bottom-Section" onclick="Unnecessary(event)">
                            <div class="Bottom-Section-Header">
                                <p>Description</p>
                                <p>Quantity</p>
                                <p>Subtotal</p>
                            </div>
            
                            <div class="Bottom-Section-List" id="BS-Order${
                              Order.id
                            }-List">
                                
                            <div class="Bottom-Section-Item">
                                    <div class="itemInfo">
                                        <svg class="LoadingRocordsSvg" width="40" height="40" viewBox="0 0 40 40" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="40" height="40" rx="2" fill="#D9D9D9" />
                                        </svg>
                                    </div>
                                    <p>
                                        <svg class="LoadingRocordsSvg" style="margin-top: 7px;" width="82" height="14"
                                            viewBox="0 0 82 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7 7H75" stroke="#D5D5D5" stroke-width="14" stroke-linecap="round" />
                                        </svg>
                                    </p>
                                    <span>
                                        <svg class="LoadingRocordsSvg" style="margin-top: 7px;" width="15" height="14"
                                            viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 7L7 7.00001" stroke="#D5D5D5" stroke-width="14" stroke-linecap="round" />
                                        </svg>
                                    </span>
                                    <span>
                                        <svg class="LoadingRocordsSvg" style="margin-top: 7px;" width="63" height="14"
                                            viewBox="0 0 63 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7 7H56" stroke="#D5D5D5" stroke-width="14" stroke-linecap="round" />
                                        </svg>
                                    </span>
                                </div>
            
                            </div>
                        </div>
                    </div>`;
          Orders_List.innerHTML += OrderHTML;
        });
        LoadingElements(false);
        if (Caller === "Orders") {
          Callitnow();
        }
      } else {
        Orders_List.innerHTML = "<p>No record found...</p>";
        LoadingElements(false);
      }
    },
    error: function (xhr, status, error) {
      console.log("Status:", status);
      console.log("Error:", error);
      console.log("Response:", xhr.responseText);
      console.log("Response JSON:", xhr.responseJSON);
    },
  });
}
