"use strict";!function(){document.querySelectorAll(".entry-content section > h2").forEach(function(n){var e=document.createElement("span");e.classList.add("icon","icon--chevron"),n.appendChild(e),n.addEventListener("click",function(e){e.preventDefault();var t=n.parentNode,e=t.hasAttribute("data-visible");t.setAttribute("data-visible",!0),e&&t.removeAttribute("data-visible")})});var e,t=document.location.hash.match(/^#section-(\d+)$/);t&&(e=t[1]-1,(t=document.querySelectorAll(".entry-content section"))[e]&&(t[e].querySelector("h2").click(),function(e){var t=e+window.pageYOffset-24;if(null!==(e=document.querySelector(".header"))&&(e=window.getComputedStyle(e),t-=parseInt(e.getPropertyValue("height"))),"scrollBehavior"in document.documentElement.style)return window.scrollTo({top:t,behavior:"smooth"});window.scrollTo(0,t)}(t[e].getBoundingClientRect().top)))}();