<?php
namespace App\Http\Controllers;

use App\Models\Product; // Importation du modèle Product
use Illuminate\Http\Request; // Importation de la classe Request pour gérer les requêtes HTTP
use Illuminate\Support\Facades\Storage; // Importation de la façade Storage pour la gestion des fichiers

class ProductController extends Controller
{
    // Affiche la liste des produits
    public function index()
    {
        $products = Product::latest()->paginate(10); // Récupère les derniers produits et les pagine par 10
        return view('products.index', compact('products')); // Retourne la vue avec les produits
    }

    // Affiche le formulaire de création d'un produit
    public function create()
    {
        return view('products.create'); // Retourne la vue pour créer un produit
    }

    // Enregistre un nouveau produit dans la base de données
    public function store(Request $request)
    {
        $validatedData = $request->validate([ // Valide les données du formulaire
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048', // Image requise et doit être un fichier image valide
            'title' => 'required|min:5', // Titre requis avec un minimum de 5 caractères
            'description' => 'required|min:10', // Description requise avec un minimum de 10 caractères
            'price' => 'required|numeric', // Prix requis et doit être numérique
            'stock' => 'required|numeric' // Stock requis et doit être numérique
        ]);

        $validatedData['image'] = $request->file('image')->store('public/products'); // Stocke l'image et ajoute le chemin à $validatedData

        Product::create($validatedData); // Crée le produit avec les données validées

        return redirect()->route('products.index')->with('success', 'Produit ajouté avec succès !'); // Redirige vers l'index avec un message de succès
    }

    // Affiche un produit spécifique
    public function show(Product $product)
    {
        return view('products.show', compact('product')); // Retourne la vue avec le produit spécifique
    }

    // Affiche le formulaire d'édition d'un produit
    public function edit(Product $product)
    {
        return view('products.edit', compact('product')); // Retourne la vue pour éditer le produit
    }

    // Met à jour un produit spécifique dans la base de données
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([ // Valide les données du formulaire
            'image' => 'image|mimes:jpeg,jpg,png|max:2048', // Image doit être un fichier image valide
            'title' => 'required|min:5', // Titre requis avec un minimum de 5 caractères
            'description' => 'required|min:10', // Description requise avec un minimum de 10 caractères
            'price' => 'required|numeric', // Prix requis et doit être numérique
            'stock' => 'required|numeric' // Stock requis et doit être numérique
        ]);

        if ($request->hasFile('image')) { // Vérifie si une nouvelle image a été téléchargée
            Storage::delete('public/products/'.$product->image); // Supprime l'ancienne image
            $validatedData['image'] = $request->file('image')->store('public/products'); // Stocke la nouvelle image et ajoute le chemin à $validatedData
        }

        $product->update($validatedData); // Met à jour le produit avec les données validées

        return redirect()->route('products.index')->with('success', 'Produit mis à jour avec succès !'); // Redirige vers l'index avec un message de succès
    }

    // Supprime un produit spécifique de la base de données
    public function destroy(Product $product)
    {
        Storage::delete('public/products/'. $product->image); // Supprime l'image du produit
        $product->delete(); // Supprime le produit

        return redirect()->route('products.index')->with('success', 'Produit supprimé avec succès !'); // Redirige vers l'index avec un message de succès
    }
}
