<?php
require("model/Autoloader.php");
Autoloader::register();

abstract class FrontController
{
	public static function home()
	{
		require("views/front/frontHomeView.php");
	}

	public static function listNounous($idDept) //liste nounous par dept
	{
		$targetNounou = new Nounou(['departement'=>$idDept]);
		$nounouManager = new NounouManager();

		$nounous = $nounouManager->listNounous($targetNounou);

		require("views/front/frontListView.php");
	}

	public static function showNounou($idNounou)
	{
		$targetNounou = new Nounou(['id'=>$idNounou]);
		$nounouManager = new NounouManager(); 
		$avisManager = new AvisManager();
		$parentManager = new ParentManager();

		$nounou = $nounouManager->getNounou($targetNounou);
		$listingAvis = $avisManager->listAvis($targetNounou);
		$noteMoyenne = $avisManager->average($targetNounou);

		$nounouToNote = new Nounou(['id'=>$idNounou, 'note'=>$noteMoyenne['AVG(note)']]);
		$nounouManager->updateNoteNounou($nounouToNote);

		//affichage de l'avis déjà enregistré
		if(isset($_SESSION['profil']) && isset($_SESSION['pseudo']) && $_SESSION['profil'] == "parent") {
			$targetAvis = new Avis(['id_nounou'=>$idNounou, 'pseudo_parent'=>$_SESSION['pseudo']]);
			$avisOnFile = $avisManager->getAvis($targetAvis);
			$parentTarget = new Parents(['pseudo'=>$_SESSION['pseudo']]);
			$parent = $parentManager->getParent($parentTarget);
		}

		//vérification si avis déjà donné
		if(isset($_SESSION['pseudo']) && $_SESSION['profil'] == 'parent')
		{
			$listPseudo_parents = [];
			foreach($listingAvis as $avis):
				$listPseudo_parents[] = $avis->pseudo_parent();
			endforeach;

			if(in_array($_SESSION['pseudo'], $listPseudo_parents))
			{
				$_SESSION['avis'] = "done";
			} else 
			{
				$_SESSION['avis'] = "clear";
			}
		}
		require("views/front/frontNounouView.php");
	}

	public static function howItWorks()
	{
		require("views/front/frontHowItWorksView.php");
	}

	public static function newNounouForm()
	{
		require("views/front/frontNewNounouFormView.php");
	}

	public static function nounouProfil($pseudoNounou)
	{
		$targetNounou = new Nounou(['pseudo'=>$pseudoNounou]);
		$nounouManager = new NounouManager();
		$nounou = $nounouManager->getNounouByPseudo($targetNounou);

		require("views/front/frontNounouProfilView.php");
	}

	public static function reportNounou($idNounou)
	{
		$nounou = new Nounou(['id'=>$idNounou]);
		$nounouManager = new NounouManager();
		$nounouManager->reportNounou($nounou);

		header("Location: index.php?action=showNounou&idNounou=".$idNounou);
	}

	public static function deleteNounou($idNounou)
	{
		$targetNounou = new Nounou(['id'=>$idNounou]);
		$targetAvis = new Avis(['id_nounou'=>$idNounou]);
		$nounouManager = new NounouManager();
		$avisManager = new AvisManager();
		$avisManager->deleteAvisByNounou($targetAvis);
		$nounouManager->deleteNounou($targetNounou);
		
		if($_SESSION['profil'] == 'admin'){
			header("Location: index.php?action=adminPanel");
		} else {
			header("Location: index.php");	
		}
	}

	public static function newParentForm()
	{
		require("views/front/frontNewParentFormView.php");
	}

	public static function parentProfil($pseudoParent)
	{
		$targetParent = new Parents(['pseudo'=>$pseudoParent]);
		$parentManager = new ParentManager();
		$parent = $parentManager->getParent($targetParent);
		
		require("views/front/frontParentProfilView.php");
	}

	public static function deleteParent($pseudoParent)
	{
		$targetParent = new Parents(['pseudo'=>$pseudoParent]);
		$targetAvis = new Avis(['pseudo_parent'=>$pseudoParent]);
		$parentManager = new ParentManager();
		$avisManager = new AvisManager();
		$avisManager->deleteAvisByParent($targetAvis);
		$parentManager->deleteParent($targetParent);
		
		if($_SESSION['profil'] == 'admin'){
			header("Location: index.php?action=adminPanel");
		} else {
			header("Location: index.php");	
		}
		
	}

	public static function login()
	{
		require("views/front/frontHomeView.php");
	}

	public static function connect()
	{
		$nounouToCheck = new Nounou(['pseudo'=>$_POST['pseudo'],'password'=>$_POST['password']]);
		$parentToCheck = new Parents(['pseudo'=>$_POST['pseudo'],'password'=>$_POST['password']]);
		$nounouManager = new NounouManager();
		$parentManager = new ParentManager();

		$nounouOnFile = $nounouManager->accessNounou($nounouToCheck);
		$parentOnFile = $parentManager->accessParent($parentToCheck);

		if(isset($nounouOnFile))
		{
			$passwordNounouToCheck = $nounouToCheck->password();
			$passwordNounouOnFile = $nounouOnFile->password();	

			if(isset($passwordNounouOnFile) && isset($passwordNounouToCheck)) 
			{
				if(password_verify($passwordNounouToCheck, $passwordNounouOnFile))
				{
					$_SESSION['profil'] = "nounou";
					$_SESSION['pseudo'] = $nounouOnFile->pseudo();
					$_SESSION['connect_message'] = "Vous êtes connecté";
					header("Location: index.php");
				} else 
				{
					$_SESSION['connect_message'] = "Pseudo ou mot de passe incorrect";
					header("Location: index.php?action=login");
				}
			} else 
			{
				$_SESSION['connect_message'] = "Pseudo ou mot de passe incorrect";
				header("Location: index.php?action=login");
			}
		} elseif(isset($parentOnFile))
		{
			$passwordParentToCheck = $parentToCheck->password();
			$passwordParentOnFile = $parentOnFile->password();	

			if(isset($passwordParentOnFile) && isset($passwordParentToCheck)) 
			{
				if(password_verify($passwordParentToCheck, $passwordParentOnFile))
				{
					$_SESSION['profil'] = "parent";
					$_SESSION['pseudo'] = $parentOnFile->pseudo();
					$_SESSION['connect_message'] = "Vous êtes connecté";
					header("Location: index.php#ancreConnect");
				} else 
				{
					$_SESSION['connect_message'] = "Pseudo ou mot de passe incorrect";
					header("Location: index.php?action=login");
				}
			} else 
			{
				$_SESSION['connect_message'] = "Pseudo ou mot de passe incorrect";
				header("Location: index.php?action=login");
			}
		} else 
		{
			$_SESSION['connect_message'] = "Pseudo ou mot de passe incorrect";
			header("Location: index.php?action=login");
		}
	}

	public static function addAvis($id_nounou)
	{
		if(isset($_POST['note']) && isset($_POST['contenu']) && is_numeric($_POST['note']) && ($_POST['note'] > 0) && ($_POST['note'] < 11) ) {
			$avis = new Avis(['id_nounou'=>$id_nounou, 'pseudo_parent'=>$_SESSION['pseudo'], 'note'=>$_POST['note'], 'contenu'=>$_POST['contenu']]);
			
			$avisManager = new AvisManager();
			$existAvis = $avisManager->existAvis($avis);
			unset($_SESSION['notice_avis']);

			if($existAvis==0) 
			{
				$avisManager->newAvis($avis);
			}
		} else {
			$_SESSION['notice_avis'] = "Saisi incorrecte";
		}
		header("Location: index.php?action=showNounou&idNounou=".$id_nounou);
	}

	public static function mailToNounou($idNounou)
	{
	
		$to = $_POST['email_nounou'];
		$pseudo_parent = $_POST['pseudo_parent'];
		$mail_parent = $_POST['email_parent'];
		$subject = "Le coin des nounous - $pseudo_parent souhaite vous contacter";
		$message = $_POST['message'];
		$messageToSend = wordwrap($message, 70, "\r\n");
		$headers = 'From: admin@lecoindesnounous.sailtheweb.com' . "\r\n" .'Reply-To:' . $mail_parent . "\r\n" . 'X-Mailer: PHP/' . phpversion();

		mail($to, $subject, $messageToSend, $headers);

		$_SESSION['info_message'] = "Votre message a été envoyé";

		header("Location: index.php?action=showNounou&idNounou=".$idNounou);
		
	}

	public static function updateAvis($id_nounou)
	{
		if(isset($_POST['note']) && isset($_POST['contenu']) && is_numeric($_POST['note']) && ($_POST['note'] > 0) && ($_POST['note'] < 11) )
		{
			$avis = new Avis(['id_nounou'=>$id_nounou, 'pseudo_parent'=>$_SESSION['pseudo'], 'note'=>$_POST['note'], 'contenu'=>$_POST['contenu']]);
			$avisManager = new AvisManager();
			$avisManager->updateAvis($avis);	
			unset($_SESSION['notice_avis']);
		} else {
			$_SESSION['notice_avis'] = "Saisi incorrecte";
		}
		header("Location: index.php?action=showNounou&idNounou=".$id_nounou);
	}

	public static function reportAvis($idAvis, $idNounou)
	{
		$avis = new Avis(['id'=>$idAvis]);
		$avisManager = new AvisManager();
		$avisManager->reportAvis($avis);

		header("Location: index.php?action=showNounou&idNounou=".$idNounou);
	}

	public static function deleteAvis($id_avis, $id_nounou)
	{
		$avis = new Avis(['id'=>$id_avis, 'id_nounou'=>$id_nounou]);
		$avisManager = new AvisManager();
		$avisManager->deleteAvis($avis);
		
		header("Location: index.php?action=showNounou&idNounou=".$avis->id_nounou());
	}

	public static function contactUs()
	{
		require("views/front/frontContactUsView.php");	
	}

	public static function mailToUs()
	{
	
		if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email']))
		{
			$to = 'nicolas5789@gmail.com';
			$name = $_POST['name'];
			$visitorMail = $_POST['email'];
			$subject = "Le coin des nounous - Message de $name";
			$message = $_POST['message'];
			$messageToSend = wordwrap($message, 70, "\r\n");
			$headers = 'From: admin@lecoindesnounous.sailtheweb.com' . "\r\n" .'Reply-To:' . $visitorMail . "\r\n" . 'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $messageToSend, $headers);

			$_SESSION['info_messageContactUs'] = "Votre message a été envoyé";
		} else {
			$_SESSION['info_messageContactUs'] = "Veuillez entrer un email valide";
		}

		header("Location: index.php?action=contactUs");	
	}	
}