const header = document.querySelector('header');

function fixedNavBar(){
    header.classList.toggle('scrolled', window.pageYOffset  > 0)
}
fixedNavBar();
window.addEventListener('scroll', fixedNavBar);

let menu = document.querySelector('#menu-btn');

menu.addEventListener('click', function(){
    let nav = document.querySelector('.navbar');
    nav.classList.toggle('active');
})

let userBtn = document.querySelector('#user-btn');

userBtn.addEventListener('click', function(){
    let userBox = document.querySelector('.profile-detail');
    userBox.classList.toggle('active');
})
