// ===============================
// GEMA AI Wizard
// Step 1 -> Step 2
// ===============================

const step1 = document.getElementById("step1");
const step2 = document.getElementById("step2");

const progress1 = document.getElementById("progress1");
const progress2 = document.getElementById("progress2");

const next1 = document.getElementById("next1");

next1.addEventListener("click", function(){

    // sembunyikan step 1
    step1.style.display = "none";

    // tampilkan step 2
    step2.style.display = "block";

    // pindahkan progress
    progress1.classList.remove("active");
    progress2.classList.add("active");

});