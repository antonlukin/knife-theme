"use strict";!function(){if("undefined"!=typeof knife_theme_custom&&(null!==document.querySelector(".entry-content")&&void 0!==knife_theme_custom.ajaxurl)){var o,i,s,r=document.createElement("form");document.querySelector(".entry-content").appendChild(r);var e=d("classes");0<e.length&&(r.className=e.join(" "));var t,n=d("heading");return 0<n.length&&((t=document.createElement("h4")).classList.add("form__heading"),t.textContent=n,r.appendChild(t)),r.classList.add("form","form--club"),r.addEventListener("submit",function(e){e.preventDefault();var t={nonce:d("nonce"),time:d("time"),name:r.querySelector('input[name="name"]').value,email:r.querySelector('input[name="email"]').value,subject:r.querySelector('input[name="subject"]').value,text:r.querySelector('textarea[name="text"]').value},n=new XMLHttpRequest;return n.open("POST",knife_theme_custom.ajaxurl+"/club"),n.setRequestHeader("Content-Type","application/json"),n.send(JSON.stringify(t)),n.onload=function(){if(a(!0),200!==n.status)return l();var e,t=JSON.parse(n.responseText);return t.success?(e=(e=t.message)||"",i.classList.add("icon--done"),o.innerHTML=e,localStorage.removeItem("knife_form_write"),r.reset()):l(t.message)},a(!1)}),function(e){var t,n,r=knife_theme_custom.fields;for(var a in r)r.hasOwnProperty(a)&&c(a,r[a],e);t=e,(n=document.createElement("div")).classList.add("form__control"),(i=document.createElement("span")).classList.add("form__control-loader","icon"),n.appendChild(i),(o=document.createElement("span")).classList.add("form__control-notice"),n.appendChild(o),(s=document.createElement("button")).classList.add("form__control-button","button"),s.innerHTML=d("button","Send"),n.appendChild(s),t.appendChild(n)}(r)}function c(e,t,n){if(void 0!==t.element){var r=document.createElement(t.element);r.classList.add("form__field-"+t.element),r.setAttribute("name",e);var a,o=document.createElement("div");for(var i in o.classList.add("form__field"),o.appendChild(r),delete t.element,t)r.setAttribute(i,t[i]);return r.value=(a=e,(JSON.parse(localStorage.getItem("knife_form_write"))||{})[a]||""),r.addEventListener("input",function(e){var t,n,r;t=this.name,n=this.value,(r=JSON.parse(localStorage.getItem("knife_form_write"))||{})[t]=n,localStorage.setItem("knife_form_write",JSON.stringify(r))}),r.addEventListener("focus",function(e){n.classList.remove("form--fold")}),n.appendChild(o)}}function a(e){return i.classList.remove("icon--loop"),void 0===e||!0===e?s.removeAttribute("disabled",""):(i.classList.remove("icon--alert","icon--done"),i.classList.add("icon--loop"),o.innerHTML="",s.setAttribute("disabled",""))}function l(e){e=e||d("warning","Request error");i.classList.add("icon--alert"),o.innerHTML=e}function d(e,t){return knife_theme_custom.hasOwnProperty(e)?knife_theme_custom[e]:t||""}}();