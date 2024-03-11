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
    public function GetFindUser(Request $request, UserRepository $userRepository)
    {

        $postdata = json_decode($request->getContent());
        if (isset($postdata->Email) && isset($postdata->Password)) {
            $email = $postdata->Email;
            $password = $postdata->password;
        } else 
            return  Utils::ErrorMissingArgumentsDebug($request->getContent());
        $var = $userRepository->findUserByEmailAndPass(['email' => $email], ['password' => $password]);
        $response = new Utils;
        return $response->GetJsonResponse($request, $var);
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
}
