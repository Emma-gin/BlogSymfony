// menu toggle
function toggleMenu() {
    const navbar = document.querySelector(".container_nav");
    const burger = document.querySelector(".burger");
    burger.addEventListener("click", () => {
        navbar.classList.toggle("show-nav");
    });
}
toggleMenu();

//modal admin page

function toggleModal() {
    const infoForAdmin = document.getElementsByClassName("more_info");
    const btnMoreInfo = document.getElementsByClassName("btn_more_info");
    const closeModalBtn = document.getElementsByClassName('close')

    for (let i = 0; i < btnMoreInfo.length; i++) {
        let btn = btnMoreInfo[i];

        btn.addEventListener("click", () => {

            for (let j = 0; j < infoForAdmin.length; j++){
                if (infoForAdmin[j].classList.contains('block')) {
                    
                    infoForAdmin[j].classList.remove("block");
                }
            }

            if (infoForAdmin[i].classList.contains('block') == false) {
                infoForAdmin[i].classList.add("block");
            } 

        });

        for (let index = 0; index < closeModalBtn.length; index++){

            closeModalBtn[index].addEventListener('click', () => {
            
            infoForAdmin[i].classList.remove("block");
        })

    }
    }


}

toggleModal();
