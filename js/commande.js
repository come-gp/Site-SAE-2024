containerMenu = document.getElementById("container-menus");
containerProduit = document.getElementById("container-produits");

btnMenu = document.getElementById("btn-menu");
btnProduit = document.getElementById("btn-produit");

function changeMenu(){
    

    

    console.log("ooooo");
    containerProduit.style.display = 'none';
    containerMenu.style.display = 'block';

    btnMenu.classList.add("btn-warning");
    btnMenu.classList.remove("btn-outline-warning");

    btnProduit.classList.add("btn-outline-warning");
    btnProduit.classList.remove("btn-warning");


}

function changeProduit(){


    console.log("ooooo");
    containerMenu.style.display = 'none';
    containerProduit.style.display = 'block';


    btnProduit.classList.add("btn-warning");
    btnProduit.classList.remove("btn-outline-warning");

    btnMenu.classList.add("btn-outline-warning");
    btnMenu.classList.remove("btn-warning");
}


