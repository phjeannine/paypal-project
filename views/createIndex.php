Simuler votre produit
<form method="POST" action="/payment/create">
	<input type="text" name="name" placeholder="Nom du produit" value="darkmira">
	<input type="number" step="0.01" min="0.01" name="price" value="1.00">
	<select name="currency">
		<option value="EUR">€</option>
		<option value="USD">$</option>
		<option value="GBP">£</option>
	</select>

	<input type="submit" class="btn btn-danger" value="envoyer">

</form>