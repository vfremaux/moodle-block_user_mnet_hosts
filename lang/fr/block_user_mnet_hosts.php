<?php

$string['user_mnet_hosts:myaddinstance'] = 'Peut ajouter une instance aux pages My';
$string['user_mnet_hosts:addinstance'] = 'Peut ajouter une instance';
$string['user_mnet_hosts:accessall'] = 'Peut accéder à tous les noeuds';

$string['accesscategory'] = 'Catégorie des attributs d\'accès';
$string['accesscategory_desc'] = 'Nom de la catégorie d\'attributs du profil pour les champs de gestion des accès';
$string['accessfieldcategory'] = 'Accès au réseau';
$string['accessfieldname'] = 'access{$a}';
$string['admin_page'] = 'Champs de contrôle d\'accès';
$string['admincat'] = 'Circulation réseau';
$string['backsettings'] = 'Revenir à la page de réglage';
$string['createdfields'] = 'Champs d\'accès créés : ';
$string['dosync'] = 'Synchroniser les champs d\'accès';
$string['errornocapacitytologremote'] = 'Vous n\'avez pas la capacité d\'utiliser le réseau Moodle';
$string['failedfields'] = 'Champs d\'accès non créés (erreurs) : ';
$string['fieldname'] = 'Accès aux plates-formes du réseau';
$string['helpsync'] = 'resynchronisation des champs d\'accès';
$string['ignoredfields'] = 'Plates-formes ignorées : ';
$string['maharapassthru'] = 'Libre accès Mahara';
$string['configmaharapassthru'] = 'Si activé, tout utilisateur du réseau Moodle pourra suivre les liens vers les Mahara enregistrés. Sinon le contrôle d\'accès sur champ de profil est encore actif pour les sites Mahara.';
$string['nohostsforyou'] = 'Aucun hôte disponible';
$string['pluginname'] = 'Controle d\'acces réseau';
$string['resync'] = 'Resynchroniser les définitions';
$string['single_full'] = 'Champs d\'accès';
$string['single_short'] = 'Synchroniser les champs d\'accès';
$string['synchonizingaccesses'] = 'Synchonisation des champs de contrôle d\'accès au réseau';
$string['syncplatforms'] = 'Si vous avez ajouté ou défini des nouveaux partenaires dans le réseau Moodle, vous devriez resynchroniser la définition des champs d\'accès pour permettre à vos utilisateurs de voir les nouvelles destinations dans le bloc "Mes sites du réseau"';
$string['user_mnet_hosts'] = 'Mes sites du réseau';
$string['masteradminoverride'] = 'L\'administrateur peut traverser le réseau';
$string['configmasteradminoverride'] = 'si vous cochez cette case, l\'administrateur de cette plate-forme pourra traverser les passages réseau et aller administrer les autres moddles. vous devrez néanmoins lui donner les droits localement pour cela. Dans un réseau de plates-formes, vous ne devrez pas avoir plus d\'un réglage de ce type activé à la fois';

$string['resync_help'] = '
<h2>Bloc de circulation contrôlée entre plates-formes</h2>
<h3>Redéfinition des champs de contrôle d\'accès</h3>

<p>Pour assurer la circulation des utilisateurs entre noeuds du réseau et contrôler
cette circulation, chaque utilisateur doit disposer d\'une marque lui permettant le 
passage pour chaque hôte du réseau.</p>
<p>Ces marques sont constituées par des champs personnalisés du profil utilisateur,
répondant à une mise en place particulière. Afin de faciliter cette mise en place,
ce script permet de restaurer automatiquement les attributs du profil manquant, en 
explorant le réseau Moodle de confiance.</p>
';
$string['errorlocaladminconstrainted'] = 'Un administrateur d\'un noeud virtuel ne peut pas circuler à travers le réseau.';
