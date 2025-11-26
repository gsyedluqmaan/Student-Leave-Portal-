const body = document.querySelector("body");
const modal = document.querySelector(".modal");
const modalButton = document.querySelector(".modal-button");
const closeButton = document.querySelector(".close-button");
const scrollDown = document.querySelector(".scroll-down");
const container = document.querySelector(".container");

const openModal = () => {
    modal.classList.add("is-open");
    body.style.overflow = "hidden";
};

const closeModal = () => {
    modal.classList.remove("is-open");
    body.style.overflow = "initial";
    scrollDown.style.display = "flex";
};

let modalTriggered = false;

window.addEventListener("scroll", () => {
    const scrollPosition = window.scrollY;
    const oneThirdHeight = window.innerHeight / 3;
    const containerHeight = container.clientHeight;

    // Trigger modal when scrolled down to 1/3 of the page height
    if (scrollPosition > oneThirdHeight && !modalTriggered) {
        scrollDown.style.display = "none";
        openModal();
        modalTriggered = true;
    }

    // Reset modal and show scroll down text when scrolling back up
    if (scrollPosition <= oneThirdHeight && modalTriggered) {
        closeModal();
        modalTriggered = false;
    }
});

modalButton.addEventListener("click", openModal);
closeButton.addEventListener("click", closeModal);

document.onkeydown = evt => {
    evt = evt || window.event;
    evt.keyCode === 27 ? closeModal() : false;
};