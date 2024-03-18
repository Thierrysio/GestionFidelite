<?php

namespace App\Controller;

use App\Entity\Blason;
use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Categorie;

use App\Repository\BlasonRepository;
use App\Repository\CommandeRepository;
use App\Repository\CommanderRepository;
use App\Repository\PalierRepository;
use App\Repository\ProduitRepository;
use App\Repository\RecompenseRepository;
use App\Repository\UserRepository;
use App\Repository\UtiliserRepository;
use App\Repository\CategorieRepository;


use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Pour Symfony 5.3 et ultérieur


class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/mobile/GetFindUser', name: 'app_api_mobile_getuser')]
    public function GetFindUser(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $postdata = json_decode($request->getContent());
        if (!isset($postdata->email) || !isset($postdata->password)) {
            return Utils::ErrorMissingArgumentsDebug($request->getContent());
        }

        $email = $postdata->email;
        $plainPassword = $postdata->password;
        $user = $userRepository->findOneByEmail($email); // Assurez-vous que cette méthode existe dans votre UserRepository

        if (!$user || !$passwordHasher->isPasswordValid($user, $plainPassword)) {
            // Si aucun utilisateur n'est trouvé OU si le mot de passe n'est pas valide
            return Utils::ErrorCustom('Email ou mot de passe invalide.');
        }

        // Si l'utilisateur est trouvé et le mot de passe est valide, renvoyez les informations de l'utilisateur
        $response = new Utils;
        $ignoredFields = ['userIdentifier','password', 'roles','lesCommandes','lesCommander','lesUtiliser','lesProduits']; // Exemple: ignorez les champs sensibles comme le mot de passe et les rôles

        return $response->GetJsonResponse($request, $user, $ignoredFields); // Excluez le champ du mot de passe hashé dans la réponse
    }

    #[Route('/api/mobile/GetAllProduits', name: 'app_api_mobile_GetAllProduits')]
    public function GetAllProduits(Request $request, ProduitRepository $produitRepository, Utils $utils): Response
    {
        try {
            $postdata = json_decode($request->getContent(), true);
            if ($postdata === null) {
                throw new \Exception('Invalid JSON.');
            }

            // Assurez-vous que l'ID de l'utilisateur est fourni
            if (!isset($postdata['Id'])) {
                return $utils->ErrorCustom('ID de l\'utilisateur manquant.');
            }
            $userId = $postdata['Id'];

            // Récupère tous les produits associés à l'utilisateur spécifique
            $produits = $produitRepository->findBy(['leUser' => $userId]);

            // Vérification si aucun produit n'a été trouvé
            if (!$produits) {
                return $utils->ErrorCustom('Aucun produit trouvé pour cet utilisateur.');
            }

            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['leUser','lesProduits'];

            return $utils->GetJsonResponse($request, $produits, $ignoredFields);
        } catch (\Exception $e) {
            return $utils->ErrorCustom('Erreur: ' . $e->getMessage());
        }
    }
    #[Route('/api/mobile/GetOneProduit', name: 'app_api_mobile_GetOneProduit')]
    public function GetOneProduit(Request $request, ProduitRepository $produitRepository , Utils $utils)
    {
        try {
            $postdata = json_decode($request->getContent(), true);
            if ($postdata === null) {
                throw new \Exception('Invalid JSON.');
            }
            $produit = $produitRepository->findOneBy(['nomProduit' => $postdata['nomProduit']]);
    
            // Vérification si aucun questionnaire n'a été trouvé
            if (!$produit) {
                return $utils->ErrorCustom('Aucun produit trouvé.');
            }
    
            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['leUser'];
    
            return $utils->GetJsonResponse($request, $produit, $ignoredFields);
        } catch (\Exception $e) {
            return $utils->ErrorCustom('Erreur: ' . $e->getMessage());
        }
    }

    #[Route('/api/mobile/setInscription', name: 'api_setInscription', methods: ['POST'])]
    public function setInscription(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, Utils $utils): Response
    {
        try {
            $postdata = json_decode($request->getContent(), true);
            if ($postdata === null) {
                throw new \Exception('Invalid JSON.');
            }

            $user = new User();
            $user->setEmail($postdata['email'] ?? null);
            $user->setNom($postdata['nom'] ?? null);
            $user->setPrenom($postdata['prenom'] ?? null);
            // Hashage du mot de passe
            $passwordHash = $userPasswordHasher->hashPassword(
                $user,
                $postdata['password'] // Assurez-vous que le champ s'appelle 'password' dans le JSON reçu
            );
            $user->setPassword($passwordHash);
            $user->setTelephone($postdata['telephone'] ?? null);
            $user->setDateNaissance(new \DateTime($postdata['dateNaissance'] ?? 'now'));
            $user->setStockPointsFidelite($postdata['StockPointsFidelite'] ?? 0);
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['userIdentifier','password', 'roles','lesCommandes','lesCommander','lesUtiliser','lesProduits']; // Exemple: ignorez les champs sensibles comme le mot de passe et les rôles

            return $utils->GetJsonResponse($request, $user, $ignoredFields);
        } catch (\Exception $e) {
            return $utils->ErrorCustom('Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());
        }
    }
    #[Route('/api/mobile/GetAllBlasons', name: 'app_api_mobile_GetAllBlasons')]
    public function GetAllBlasons(Request $request, BlasonRepository $blasonRepository , Utils $utils)
    {
        try {
            $blasons = $blasonRepository->findAll();
    
            // Vérification si aucun blason n'a été trouvé
            if (!$blasons) {
                return $utils->ErrorCustom('Aucun produit trouvé.');
            }
    
            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['lesUser'];
    
            return $utils->GetJsonResponse($request, $blasons, $ignoredFields);
        } catch (\Exception $e) {
            return $utils->ErrorCustom('Erreur: ' . $e->getMessage());
        }
    }
    #[Route('/api/mobile/GetAllCommandes', name: 'app_api_mobile_GetAllCommandes')]
    public function GetAllCommandes(Request $request, CommandeRepository $commandeRepository , Utils $utils)
    {
        try {
            $commandes = $commandeRepository->findAll();
    
            // Vérification si aucune commande n'a été trouvée
            if (!$commandes) {
                return $utils->ErrorCustom('Aucune commande trouvée.');
            }
    
            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['leUser','lesCommander'];
    
            return $utils->GetJsonResponse($request, $commandes, $ignoredFields);
        } catch (\Exception $e) {
            return $utils->ErrorCustom('Erreur: ' . $e->getMessage());
        }

    }
    #[Route('/api/mobile/GetAllCategories', name: 'app_api_mobile_GetAllCategories', methods: ['POST'])]
    public function GetAllCategories(Request $request, CategorieRepository $categorieRepository , Utils $utils)
    {
        try {
            $postdata = json_decode($request->getContent(), true);
            if ($postdata === null) {
                throw new \Exception('Invalid JSON.');
            }
             // Assurez-vous que l'ID de l'utilisateur est fourni
             if (!isset($postdata['Id'])) {
                return $utils->ErrorCustom('ID de l\'utilisateur manquant.');
            }
            $userId = $postdata['Id'];

            // Récupère tous les produits associés à l'utilisateur spécifique
            $categories = $categorieRepository->findBy(['leUser' => $userId]);
         
    
            // Vérification si aucune categorie n'a été trouvée
            if (!$categories) {
                return $utils->ErrorCustom('Aucune categorie trouvée.');
            }
    
            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['lesProduits','leUser'];
    
            return $utils->GetJsonResponse($request, $categories, $ignoredFields);
        } catch (\Exception $e) {
            return $utils->ErrorCustom('Erreur: ' . $e->getMessage());
        }

    }
    #[Route('/api/mobile/creerProduit', name: 'api_CreerProduit', methods: ['POST'])]
    public function CreerProduit(Request $request, ProduitRepository $produitRepository, UserRepository $userRepository,CategorieRepository $categorieRepository , EntityManagerInterface $entityManager, Utils $utils): Response
    {
        try {
            $postdata = json_decode($request->getContent(), true);
            if ($postdata === null) {
                throw new \Exception('Invalid JSON.');
            }

            // Récupération de la categorie à partir de l'ID fourni
            if (!isset($postdata['categorieId'])) {
                throw new \Exception('Categorie ID is missing.');
            }
            $categorie = $categorieRepository->find($postdata['categorieId']);
            if (!$categorie) {
                throw new \Exception('User not found.');
            }
            // Récupération du User à partir de l'ID fourni
            if (!isset($postdata['Id'])) {
                throw new \Exception('User ID is missing.');
            }
            $user = $userRepository->find($postdata['Id']);
            if (!$user) {
                throw new \Exception('User not found.');
            }

            $produit = new Produit();
            if (isset($postdata['nomProduit'])) {
                $produit->setNomProduit($postdata['nomProduit']);
            }
            if (isset($postdata['prixProduit'])) {
                $produit->setPrixProduit($postdata['prixProduit']);
            }
            if (isset($postdata['pointsFidelite'])) {
                $produit->setPointsFidelite($postdata['pointsFidelite']);
            }

            // Association de l'utilisateur au produit
            $produit->setLeUser($user);
             // Association de la categorie au produit
             $produit->setLaCategorie($categorie);

            $entityManager->persist($produit);
            $entityManager->flush();

            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['lesProduits','lesCommandes','lesCommander','lesUtiliser']; // Ajustez selon votre besoin.

            return $utils->GetJsonResponse($request, $produit, $ignoredFields);
        } catch (\Exception $e) {
            return $utils->ErrorCustom('Erreur lors de la création du produit: ' . $e->getMessage());
        }
    }

    #[Route('/api/mobile/creerCategorie', name: 'api_creerCategorie', methods: ['POST'])]
    public function creerCategorie(Request $request,UserRepository $userRepository, CategorieRepository $categorieRepository, EntityManagerInterface $entityManager, Utils $utils): Response
    {
        try {
            $postdata = json_decode($request->getContent(), true);
            if ($postdata === null) {
                throw new \Exception('Invalid JSON.');
            }
             // Récupération du User à partir de l'ID fourni
             if (!isset($postdata['Id'])) {
                throw new \Exception('User ID is missing.');
            }
            $user = $userRepository->find($postdata['Id']);
            if (!$user) {
                throw new \Exception('User not found.');
            }

            // Création de la nouvelle catégorie
            $categorie = new Categorie();
            if (isset($postdata['nomCategorie'])) {
                $categorie->setNomCategorie($postdata['nomCategorie']);
            }

            $categorie->setLeUser($user);

            $entityManager->persist($categorie);
            $entityManager->flush();

            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['lesProduits','leUser']; // Ajustez selon votre besoin.

            return $utils->GetJsonResponse($request, $categorie, $ignoredFields);
        } catch (\Exception $e) {
            return $utils->ErrorCustom('Erreur lors de la création de la catégorie: ' . $e->getMessage());
        }
    }
}
