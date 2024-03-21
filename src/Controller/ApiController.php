<?php

namespace App\Controller;

use App\Entity\Blason;
use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Commander;
use App\Entity\Utiliser;

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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
            $postdata = json_decode($request->getContent(), true);
            if ($postdata === null) {
                throw new \Exception('Invalid JSON.');
            }

            // Récupère tous les produits associés à l'utilisateur spécifique
            $blasons = $blasonRepository->findAll();
    
            // Vérification si aucun blason n'a été trouvé
            if (!$blasons) {
                return $utils->ErrorCustom('Aucun blason trouvé.');
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
    public function CreerProduit(Request $request, ProduitRepository $produitRepository, UserRepository $userRepository, CategorieRepository $categorieRepository, EntityManagerInterface $entityManager, Utils $utils): Response
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
    
            // Gestion de l'imageUrl de manière optionnelle
            if (isset($postdata['imageUrl'])) {
                $produit->setImageUrl($postdata['imageUrl']);
            } else {
                //une image par defaut
            }
    
            // Association de l'utilisateur au produit
            $produit->setLeUser($user);
    
            // Association de la categorie au produit si categorieId est fourni
            if (isset($postdata['categorieId'])) {
                $categorie = $categorieRepository->find($postdata['categorieId']);
                if (!$categorie) {
                    throw new \Exception('Category not found.');
                }
                $produit->setLaCategorie($categorie);
            }
    
            $entityManager->persist($produit);
            $entityManager->flush();
    
            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['leUser','lesProduits','lesCommandes','lesCommander','lesUtiliser']; // Ajustez selon votre besoin.
    
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

    #[Route('/api/blason/creerBlason', name: 'creer_Blason', methods: ['POST'])]
    public function creerBlason(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, Utils $utils): Response
    {
        try {
            $postData = json_decode($request->getContent(), true);
            if ($postData === null) {
                throw new \Exception('Invalid JSON.');
            }

            // Création du blason
            $blason = new Blason();
            $blason->setNomBlason($postData['nomBlason'] ?? '');
            $blason->setMontantAchats($postData['montantAchats'] ?? 0);

            $entityManager->persist($blason);
            $entityManager->flush();

            // Réponse réussie
            $ignoredFields = ['lesUser']; // Ajustez selon votre besoin.

            return $utils->GetJsonResponse($request, $blason, $ignoredFields);
        } catch (\Exception $e) {
            return $utils->ErrorCustom('Erreur lors de la création du blason: ' . $e->getMessage());
        }
    }

    #[Route('/api/mobile/updateUser', name: 'api_update_user', methods: ['POST'])]
    public function updateUser(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, Utils $utils): Response
    {
        try {
            $postdata = json_decode($request->getContent(), true);
            if ($postdata === null) {
                throw new \Exception('Invalid JSON.');
            }
    
            if (!isset($postdata['id'])) {
                throw new \Exception('User ID is missing.');
            }
    
            $user = $userRepository->find($postdata['id']);
            if (!$user) {
                throw new \Exception('User not found.');
            }
    
            // Mise à jour des attributs de l'utilisateur avec les données reçues
            if (isset($postdata['email'])) {
                $user->setEmail($postdata['email']);
            }
            if (isset($postdata['roles'])) {
                $user->setRoles($postdata['roles']);
            }
            if (isset($postdata['nom'])) {
                $user->setNom($postdata['nom']);
            }
            if (isset($postdata['prenom'])) {
                $user->setPrenom($postdata['prenom']);
            }
            if (isset($postdata['dateNaissance'])) {
                // Assurez-vous de convertir la date en un objet \DateTime
                $dateNaissance = new \DateTime($postdata['dateNaissance']);
                $user->setDateNaissance($dateNaissance);
            }
            if (isset($postdata['telephone'])) {
                $user->setTelephone($postdata['telephone']);
            }
            if (isset($postdata['stockPointsFidelite'])) {
                $user->setStockPointsFidelite($postdata['StockPointsFidelite']);
            }
            if (isset($postdata['leBlason'])) {
                $user->setLeBlason($postdata['leBlason']);
            }
            // Validation des données
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return new Response($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST);
            }
    
            $entityManager->flush();

            // Spécifiez ici les champs que vous souhaitez ignorer dans la réponse JSON
            $ignoredFields = ['userIdentifier','password', 'roles','lesCommandes','lesCommander','lesUtiliser','lesProduits']; // Exemple: ignorez les champs sensibles comme le mot de passe et les rôles
    
            // Utilisation de $utils->GetJsonResponse pour retourner l'utilisateur modifié
            return $utils->GetJsonResponse($request, $user, $ignoredFields);
        } catch (\Exception $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), Response::HTTP_BAD_REQUEST);
        }
    }
    
    #[Route('/api/mobile/creerCommande', name: 'api_creer_commande', methods: ['POST'])]
    public function creerCommande(Request $request, UserRepository $userRepository, CommandeRepository $commandeRepository, EntityManagerInterface $entityManager, Utils $utils): Response
    {
        try {
            $postdata = json_decode($request->getContent(), true);
            if ($postdata === null) {
                throw new \Exception('Invalid JSON.');
            }
    
            if (!isset($postdata['Id'])) {
                throw new \Exception('User ID is missing.');
            }
            
            $user = $userRepository->find($postdata['Id']);
            if (!$user) {
                throw new \Exception('User not found.');
            }
    
            $commande = new Commande();
            $commande->setDateCommande(new \DateTime()); // Assumer la date de commande comme étant maintenant
            $commande->setLeUser($user);
    
            // Ici, ajoutez toute autre logique spécifique, comme la gestion des éléments de la commande
            
            $entityManager->persist($commande);
            $entityManager->flush();
    
            // Utilisation de $utils->GetJsonResponse pour retourner la commande créée
            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['leUser', 'lesCommander']; // Ajustez selon votre besoin
            
            return $utils->GetJsonResponse($request, $commande, $ignoredFields);
        } catch (\Exception $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), Response::HTTP_BAD_REQUEST);
        }
    }
    #[Route('/api/mobile/creerCommander', name: 'api_creer_commander', methods: ['POST'])]
public function creerCommander(Request $request, ProduitRepository $produitRepository, CommandeRepository $commandeRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, Utils $utils): Response
{
    try {
        $postdata = json_decode($request->getContent(), true);
        if ($postdata === null) {
            throw new \Exception('Invalid JSON.');
        }

        // Récupération et vérification de l'existence du Produit, de l'User et de la Commande
        $produit = $produitRepository->find($postdata['leProduit'] ?? null);
        $commande = $commandeRepository->find($postdata['laCommande'] ?? null);

        if (!$produit || !$commande) {
            throw new \Exception('Produit, User, or Commande not found.');
        }

        $commander = new Commander();
        $commander->setLeProduit($produit);
        $commander->setLaCommande($commande);
        $commander->setQuantite($postdata['quantite'] ?? 0);

        $entityManager->persist($commander);
        $entityManager->flush();

        // Répondre avec l'entité `Commander` créée, ajustez les champs à ignorer selon le besoin
        return $utils->GetJsonResponse($request, $commander, ['leUser', 'laCommande', 'leProduit']);
    } catch (\Exception $e) {
        return new Response(json_encode(['error' => $e->getMessage()]), Response::HTTP_BAD_REQUEST);
    }
}
#[Route('/api/mobile/creerUtiliser', name: 'api_creer_utiliser', methods: ['POST'])]
public function creerUtiliser(Request $request, RecompenseRepository $recompenseRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, Utils $utils): Response
{
    try {
        $postdata = json_decode($request->getContent(), true);
        if ($postdata === null) {
            throw new \Exception('Invalid JSON.');
        }

        if (!isset($postdata['laRecompense'])) {
            throw new \Exception('Recompense ID is missing.');
        }
        
        if (!isset($postdata['leUser'])) {
            throw new \Exception('User ID is missing.');
        }

        $recompense = $recompenseRepository->find($postdata['laRecompense']);
        if (!$recompense) {
            throw new \Exception('Recompense not found.');
        }

        $user = $userRepository->find($postdata['leUser']);
        if (!$user) {
            throw new \Exception('User not found.');
        }

        $utiliser = new Utiliser();
        $utiliser->setLaRecompense($recompense);
        $utiliser->setLeUser($user);
        $utiliser->setDateUtiliser(new \DateTime()); // Assumer la date d'utilisation comme étant maintenant

        $entityManager->persist($utiliser);
        $entityManager->flush();

        // Utilisation de $utils->GetJsonResponse pour retourner l'entité Utiliser créée
        // Spécifiez ici les champs à ignorer si nécessaire
        $ignoredFields = ['laRecompense', 'leUser']; // Ajustez selon votre besoin
        
        return $utils->GetJsonResponse($request, $utiliser, $ignoredFields);
    } catch (\Exception $e) {
        return new Response(json_encode(['error' => $e->getMessage()]), Response::HTTP_BAD_REQUEST);
    }
}
}
