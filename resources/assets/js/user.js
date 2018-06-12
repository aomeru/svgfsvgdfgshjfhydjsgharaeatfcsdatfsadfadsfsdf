require('./bootstrap');

window.Vue = require('vue');

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app'
});

$(function(){
    //console.log('hello world')
});


const accordions = document.getElementsByClassName('has-sub-nav')
const adminSlideButton = document.getElementById('portal-mobile-menu-button')

function setSubmenuStyles(submenu, maxHeight, margins) {
    submenu.style.maxHeight = maxHeight
    submenu.style.marginTop = margins
    submenu.style.marginBottom = margins
}

adminSlideButton.onclick = function () {
    this.classList.toggle('is-active');
    document.getElementById('portal-left').classList.toggle('is-active');
}

for (var i = 0; i < accordions.length; i++) {
    if (accordions[i].classList.contains('sub-active')) {
        const submenu = accordions[i].nextElementSibling
        var x = 41.6
        h = submenu.childElementCount * x
        // setSubmenuStyles(submenu,submenu.scrollHeight + "px", "0em")
        setSubmenuStyles(submenu,h + "px", "0em")
        console.log(submenu.scrollHeight);
        console.log(h);
        // console.log(document.getElementById('testleave').scrollHeight);
        // console.log(document.getElementById('testuser').scrollHeight);
    }

    accordions[i].onclick = function () {
        this.classList.toggle('sub-active')

        const submenu = this.nextElementSibling
        if (submenu.style.maxHeight) {
            // menu is open, we need to close it now
            setSubmenuStyles(submenu, null, null)
        } else {
            // meny is close, so we need to open it
            setSubmenuStyles(submenu, submenu.scrollHeight + "px", "0em")
        }
    }
}
