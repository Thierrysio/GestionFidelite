<?php

namespace App\Controller;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;

use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\Collection;

use App\Entity\User;
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
    public function GetAllProduits(Request $request, ProduitRepository $produitRepository , Utils $utils)
    {
        try {
            $produits = $produitRepository->findAll();
    
            // Vérification si aucun questionnaire n'a été trouvé
            if (!$produits) {
                return $utils->ErrorCustom('Aucun produit trouvé.');
            }
    
            // Spécifiez ici les champs à ignorer si nécessaire
            $ignoredFields = ['leUser'];
    
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
}
