///// MULTIPLIER LE PRIX PAR LA QUANTITE /////

document.addEventListener("change", function (event) {
  function updatePrice(element) {
    const parent = element.parentElement;
    const selectedOption = parent.querySelector('select[name="product_id[]"]')
      .options[
      parent.querySelector('select[name="product_id[]"]').selectedIndex
    ];
    const priceInput = parent.querySelector('input[name="prix[]"]');
    const quantityInput = parent.querySelector('input[name="quantity[]"]');
    priceInput.value =
      selectedOption.getAttribute("data-price") * quantityInput.value;
  }

  if (event.target && event.target.classList.contains("product_id")) {
    updatePrice(event.target);
  } else if (event.target && event.target.name === "quantity[]") {
    updatePrice(event.target);
  }
});

//   //// SUPPRIMER UN ITEM DE LA COMMANDE ////

// document.addEventListener("click", function (e) {
//     if (e.target && e.target.classList.contains("removeItemButton")) {
//         e.target.parentElement.remove();
//     }
//     });



//// AFFICHER LA COMMANDE ////