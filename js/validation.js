console.log("1");


function changePrix(){
    //console.log("oui");
    var nouveauPrix = document.getElementById("nouveau-prix");
    var ancienPrix = document.getElementById("ancien-prix");
    var nbPointsUtilises = document.getElementById("nbPointsUtilises");


    nouveauPrix.style.display = 'block';
    console.log(nouveauPrix.innerHTML);
    console.log(ancienPrix.innerHTML);
    console.log(nbPointsUtilises.value);
    
    nouveauPrix.innerHTML = "( nouveau prix : " + Math.round((ancienPrix.innerHTML - nbPointsUtilises.value / 100) * 100) / 100 + "€ )";
 ;
}