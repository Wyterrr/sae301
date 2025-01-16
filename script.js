//// AJOUTER UN NOUVEL ITEM DANS LA COMMANDE ////

document.getElementById("addItemButton").addEventListener("click", function () {
  const itemsDiv = document.getElementById("items");
  const newItem = document.createElement("div");
  newItem.classList.add("item");
  newItem.innerHTML = `
        <label>Produit :</label>
        <select name="product_id[]" required>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo htmlspecialchars($product['id']); ?>">
                    <?php echo htmlspecialchars($product['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label>Quantité :</label>
        <input type="number" name="quantity[]" min="1" value="1" required><br><br>
    `;
  itemsDiv.appendChild(newItem);
});


///// MULTIPLIER LE PRIX PAR LA QUANTITE /////
document.addEventListener("change", function (event) {
    function updatePrice(element) {
      const parent = element.parentElement;
      const selectedOption = parent.querySelector('select[name="product_id[]"]')
        .options[
        parent.querySelector('select[name="product_id[]"]').selectedIndex
      ];
      const priceInput = parent.querySelector('input[name="price[]"]');
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

    