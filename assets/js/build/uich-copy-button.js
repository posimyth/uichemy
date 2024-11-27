(()=>{"use strict";var e={n:t=>{var i=t&&t.__esModule?()=>t.default:()=>t;return e.d(i,{a:i}),i},d:(t,i)=>{for(var s in i)e.o(i,s)&&!e.o(t,s)&&Object.defineProperty(t,s,{enumerable:!0,get:i[s]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const t=window.ReactDOM;var i=e.n(t);const s=window.ReactJSXRuntime,{Modal:c}=wp.components;!function(e,t){const o='<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.15443 7.91705L8.14325 10.9265C8.14325 10.9265 5.60113 11.0735 5.60113 9.11541V4.12304C5.60071 3.162 5.21864 2.24047 4.53893 1.56107C3.85923 0.881661 2.93753 0.5 1.9765 0.5H3.17421e-07V10.4512C-0.000209837 10.9806 0.103934 11.5048 0.30647 11.9939C0.509006 12.483 0.805964 12.9274 1.18037 13.3016C1.55477 13.6759 1.99926 13.9727 2.48845 14.175C2.97763 14.3773 3.5019 14.4813 4.03128 14.4809C4.30471 14.5064 4.57992 14.5064 4.85335 14.4809H9.66757C10.7509 14.4809 11.7899 14.0505 12.556 13.2844C13.322 12.5184 13.7524 11.4794 13.7524 10.3961V7.91945L8.15443 7.91705ZM13.2674 10.3937C13.2655 11.3465 12.8862 12.2599 12.2124 12.9337C11.5386 13.6074 10.6252 13.9868 9.67235 13.9887H7.38669C7.62987 13.794 7.8426 13.564 8.01782 13.3065C8.55948 12.5076 8.62818 11.6136 8.62818 10.9257L8.63857 8.40117H13.2674V10.3937Z" fill="white"/><path d="M11.413 0.5C10.5485 0.5 9.71933 0.843391 9.10792 1.45465C8.49652 2.06591 8.15293 2.89498 8.15271 3.75954V5.71446H13.7546V0.5H11.413Z" fill="white"/></svg> Paste';function a(){if(!document.querySelector("#uich-paste-area-input")){const e=document.querySelector("#uich-paste-clipboard");i().render(n(),e),e.innerHTML=o;const s=document.querySelector("#uich-paste-area-input");s.focus(),s.addEventListener("paste",(async function(e){e.preventDefault(),r();const i=e.clipboardData.getData("text");if(i&&/<!--\s?\/?wp:tpgb\/[a-z-]+\s?.*?-->/g.test(i)){const e=t.blocks.parse(i),s=JSON.stringify(e),c=/\.(jpg|png|jpeg|gif|svg)/gi.test(s);document.querySelector("#uich-paste-clipboard").innerHTML='<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 38 38" stroke="#6f14f1"><g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="2"><circle stroke-opacity=".3" cx="18" cy="18" r="18"></circle><path d="M36 18c0-9.94-8.06-18-18-18"><animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"></animateTransform></path></g></g></svg>',c?jQuery.ajax({url:tpgb_blocks_load.ajax_url,method:"POST",data:{nonce:tpgb_admin.tpgb_nonce,action:"tpgb_cross_cp_import",content:s}}).done((function(e){if(e.success){document.querySelector("#uich-paste-clipboard").innerHTML=o;const i=e.data[0];t.data.dispatch("core/block-editor").insertBlocks(i)}})):(document.querySelector("#uich-paste-clipboard").innerHTML=o,t.data.dispatch("core/block-editor").insertBlocks(e))}}))}}t.data.subscribe((function(){const e=document.querySelector(".edit-post-header-toolbar .editor-document-tools__left");e&&setTimeout((()=>{if(!e.querySelector("#uich-paste-clipboard")){const t=document.createElement("div");t.classList.add("uich-paste-clipboard-wrap");const i='<button id="uich-paste-clipboard" title="Paste">'+o+"</button>";t.innerHTML=i,e.appendChild(t);let s=document.querySelector("#uich-paste-clipboard");s&&s.addEventListener("click",a)}}),1)}));const n=()=>(0,s.jsx)(c,{className:"uich-clipboard-paste-data",onRequestClose:r,children:(0,s.jsxs)("div",{className:"uich-clip-pop-content",children:[(0,s.jsxs)("svg",{width:"84",height:"85",viewBox:"0 0 84 85",fill:"none",xmlns:"http://www.w3.org/2000/svg",children:[(0,s.jsxs)("g",{clipPath:"url(#clip0_4829_3031)",children:[(0,s.jsx)("path",{d:"M36.75 41.625C36.75 35.353 41.853 30.25 48.125 30.25H59.5V20.625C59.5 15.3155 55.1845 11 49.875 11H9.625C4.3155 11 0 15.3155 0 20.625V64.375C0 69.6845 4.3155 74 9.625 74H36.75V41.625Z",fill:"#1E1E1E"}),(0,s.jsx)("path",{d:"M37.6641 7.88199L37.9528 8.9H39.011H42.875C43.5508 8.9 44.1 9.4492 44.1 10.125V17.125C44.1 19.7293 41.9793 21.85 39.375 21.85H20.125C17.5207 21.85 15.4 19.7293 15.4 17.125V10.125C15.4 9.4492 15.9492 8.9 16.625 8.9H20.489H21.5472L21.8359 7.88199C22.8143 4.43198 25.9974 1.9 29.75 1.9C33.5026 1.9 36.6857 4.43198 37.6641 7.88199Z",fill:"#1E1E1E",stroke:"white",strokeWidth:"2.8"}),(0,s.jsx)("path",{d:"M77.875 35.5H48.125C44.744 35.5 42 38.244 42 41.625V78.375C42 81.756 44.744 84.5 48.125 84.5H77.875C81.256 84.5 84 81.756 84 78.375V41.625C84 38.244 81.256 35.5 77.875 35.5Z",fill:"#A5A5A5"}),(0,s.jsx)("path",{d:"M70.4375 67H54.6875C53.2385 67 52.0625 65.824 52.0625 64.375C52.0625 62.926 53.2385 61.75 54.6875 61.75H70.4375C71.8865 61.75 73.0625 62.926 73.0625 64.375C73.0625 65.824 71.8865 67 70.4375 67Z",fill:"white"}),(0,s.jsx)("path",{d:"M70.4375 56.5H54.6875C53.2385 56.5 52.0625 55.324 52.0625 53.875C52.0625 52.426 53.2385 51.25 54.6875 51.25H70.4375C71.8865 51.25 73.0625 52.426 73.0625 53.875C73.0625 55.324 71.8865 56.5 70.4375 56.5Z",fill:"white"})]}),(0,s.jsx)("defs",{children:(0,s.jsx)("clipPath",{id:"clip0_4829_3031",children:(0,s.jsx)("rect",{width:"84",height:"84",fill:"white",transform:"translate(0 0.5)"})})})]}),(0,s.jsxs)("div",{className:"uich-clip-os-wrap",children:[(0,s.jsxs)("div",{className:"uich-os-tag",children:[(0,s.jsx)("label",{children:"For Mac :"}),(0,s.jsxs)("span",{className:"os-icon",children:[(0,s.jsxs)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"32",height:"32",fill:"none",viewBox:"0 0 32 32",children:[(0,s.jsx)("rect",{width:"32",height:"32",fill:"#1C1C1C",rx:"5"}),(0,s.jsx)("path",{fill:"#fff",d:"M20.8 17.6h-1.6v-3.2h1.6c1.765 0 3.2-1.436 3.2-3.2C24 9.436 22.565 8 20.8 8a3.203 3.203 0 0 0-3.2 3.2v1.6h-3.2v-1.6c0-1.764-1.435-3.2-3.2-3.2A3.203 3.203 0 0 0 8 11.2c0 1.764 1.435 3.2 3.2 3.2h1.6v3.2h-1.6A3.203 3.203 0 0 0 8 20.8c0 1.764 1.435 3.2 3.2 3.2 1.765 0 3.2-1.436 3.2-3.2v-1.6h3.2v1.6c0 1.764 1.435 3.2 3.2 3.2 1.765 0 3.2-1.436 3.2-3.2 0-1.765-1.435-3.2-3.2-3.2Zm-1.6-6.4c0-.882.718-1.6 1.6-1.6.882 0 1.6.718 1.6 1.6 0 .882-.718 1.6-1.6 1.6h-1.6v-1.6Zm-6.4 9.6c0 .882-.718 1.6-1.6 1.6-.882 0-1.6-.718-1.6-1.6 0-.882.718-1.6 1.6-1.6h1.6v1.6Zm0-8h-1.6c-.882 0-1.6-.718-1.6-1.6 0-.882.718-1.6 1.6-1.6.882 0 1.6.718 1.6 1.6v1.6Zm4.8 4.8h-3.2v-3.2h3.2v3.2Zm3.2 4.8c-.882 0-1.6-.718-1.6-1.6v-1.6h1.6c.882 0 1.6.718 1.6 1.6 0 .882-.718 1.6-1.6 1.6Z"})]})," + V"]})]}),(0,s.jsx)("div",{className:"uich-clip-separator",children:"|"}),(0,s.jsxs)("div",{className:"uich-os-tag",children:[(0,s.jsx)("label",{children:"For Windows :"}),(0,s.jsxs)("span",{className:"os-icon",children:[(0,s.jsxs)("svg",{xmlns:"http://www.w3.org/2000/svg",width:"32",height:"32",fill:"none",viewBox:"0 0 32 32",children:[(0,s.jsx)("rect",{width:"32",height:"32",fill:"#1C1C1C",rx:"5"}),(0,s.jsx)("path",{fill:"#fff",d:"M8 10.5v5h7V9.625L8 10.5ZM16 9.5v6h8v-7l-8 1ZM16 16.5v6l8 1v-7h-8ZM8 16.5v5l7 .875V16.5H8Z"})]})," + V"]})]})]}),(0,s.jsx)("div",{className:"uich-head-clip-pop",children:"To paste with images from UiChemy"}),(0,s.jsx)("input",{type:"text",id:"uich-paste-area-input",autoComplete:"off"})]})});function r(){const e=document.querySelector("#uich-paste-clipboard");e&&(i().unmountComponentAtNode(e),e.innerHTML=o)}}(window,wp)})();