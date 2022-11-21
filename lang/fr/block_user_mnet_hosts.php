<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

$string['user_mnet_hosts:myaddinstance'] = 'Peut ajouter une instance aux pages My';
$string['user_mnet_hosts:addinstance'] = 'Peut ajouter une instance';
$string['user_mnet_hosts:accessall'] = 'Peut accéder à tous les noeuds';

$string['accesscategory'] = 'Catégorie des attributs d\'accès';
$string['accesscategory_desc'] = 'Nom de la catégorie d\'attributs du profil pour les champs de gestion des accès';
$string['accesscategorydefault'] = 'Attributs de contrôle d\'accès réseau';
$string['accessfieldcategory'] = 'Accès au réseau';
$string['accessfieldname'] = 'access{$a}';
$string['admincat'] = 'Circulation réseau';
$string['adminpage'] = 'Champs de contrôle d\'accès';
$string['admintitle'] = 'Définir les champs d\'accès aux pairs du réseau';
$string['backsettings'] = 'Revenir à la page de réglage';
$string['configaccesssource'] = 'Origine des définitions d\'hôtes';
$string['configaccesssource_desc'] = 'Choisit à partir de quelle source de données le bloc constitue sa liste d\'hôtes à contrôler par des champs d\'accès.';
$string['configallowremoteadmins'] = 'Autorise les administrateurs distants';
$string['configallowremoteadmins_desc'] = 'Permet à des utilisateurs du réseau (mnet) d\'être nommés administrateurs de site (customscript).';
$string['configdisablemnetimportfilter'] = 'Désactiver les filtres d\'import MNET';
$string['configdisplaylimit'] = 'Limite d\'affichage';
$string['configdisplaylimit_desc'] = 'Définit le nombre maximum de liens à afficher avant de demander le filtrage.';
$string['configkeydeepness'] = 'Profondeur du nom du champ d\'accès';
$string['configkeydeepness_desc'] = 'Le nombre de tokens du nom de domaine utilisés pour forger le nom du champ d\'accès et le libellé.';
$string['configlocaladminoverride'] = 'L\'administrateur local peut traverser';
$string['configlocaladminoverride_desc'] = 'Si non marqué, l\'administrateur local "admin" reste contraint dans le site local (conseillé).';
$string['configmaharapassthru'] = 'Libre accès Mahara';
$string['configmaharapassthru_desc'] = 'Si activé, tout utilisateur du réseau Moodle pourra suivre les liens vers les Mahara enregistrés. Sinon le contrôle d\'accès sur champ de profil est encore actif pour les sites Mahara.';
$string['confignewwindow'] = 'Navigation';
$string['confignewwindow_desc'] = 'Vous pouvez choisir d\'ouvrir la destination dans une nouvelle fenêtre.';
$string['configsingleaccountcheck'] = 'Identité MNET unique';
$string['configsingleaccountcheck_desc'] = 'Ce réglage ajoute un contrôle supplémentaire au plugin d\'authentification MNET afin de s\'assurer qu\'un utilisateur dispose un compte unique entre son identité locale et son identité réseau.';
$string['configforceauth'] = 'Authentification contrainte';
$string['configforceauth_desc'] = 'Si défini, alors seuls les comptes sur cette source d\'authentification pourront être trouvés pour la résolution de l\'identité unique.';
$string['configdisablemnetimportfilter'] = 'Désactiver le filtre d\'import de champs de profil';
$string['configldapaccessattributes'] = 'Attributs LDAP des accès';
$string['configldaphostpatterns'] = 'Motifs d\'extraction du marqueur d\'hôte';
$string['confighostwwwrootmask'] = 'Masque d\'URL racine de l\'hôte accessible';
$string['createdfields'] = 'Champs d\'accès créés : ';
$string['dosync'] = 'Synchroniser les champs d\'accès';
$string['errornocapacitytologremote'] = 'Vous n\'avez pas la capacité d\'utiliser le réseau Moodle';
$string['failedfields'] = 'Champs d\'accès non créés (erreurs) : ';
$string['fieldkey'] = 'Code de champ';
$string['fieldname'] = 'Accès aux plates-formes du réseau';
$string['filter'] = 'Filtrer';
$string['ldapmapping'] = 'Alimentation LDAP';
$string['helpsync'] = 'resynchronisation des champs d\'accès';
$string['ignoredfields'] = 'Plates-formes ignorées : ';
$string['mnetaccess_description'] = 'Un service permettant de vérifier les droits d\'accès sur un noeud distant';
$string['mnetaccess_name'] = 'Contrôle d\'accès MNET';
$string['mnetaccess_service_description'] = 'Un service permettant de vérifier les droits d\'accès sur un noeud distant';
$string['mnetaccess_service_name'] = 'Conrôle d\'accès MNET';
$string['mnetbehaviour'] = 'Comportement du réseau MNET';
$string['mnetmodifiers'] = 'Modificateur du réseau MNET';
$string['mnetsource'] = 'Prendre les hôtes MNET actifs';
$string['nohostsforyou'] = 'Aucun hôte disponible';
$string['noauthcheck'] = 'Pas de contrôle de source d\'authentification';
$string['pluginname'] = 'Controle d\'acces réseau';
$string['resync'] = 'Resynchroniser les définitions';
$string['syncfull'] = 'Outil de création et synchronisation des champs de contrôle d\'accès';
$string['synchonizingaccesses'] = 'Synchonisation des champs de contrôle d\'accès au réseau';
$string['syncplatforms'] = 'Si vous avez ajouté ou défini des nouveaux partenaires dans le réseau Moodle, vous devriez resynchroniser la définition des champs d\'accès pour permettre à vos utilisateurs de voir les nouvelles destinations dans le bloc "Mes sites du réseau"';
$string['syncshort'] = 'Synchronisation des champs de contrôle d\'accès';
$string['usefiltertoreduce'] = '... autres hôtes non visibles. Réduire avec le filtre...';
$string['user_mnet_hosts'] = 'Mes sites du réseau';
$string['vmoodleandmnetsource'] = 'Prendre les définitions VMoodle actives, et ajouter les hôtes mnet supplémentaires.';
$string['newwindow'] = 'Nouvelle fenêtre';
$string['newwindow_desc'] = 'Si actif, la navigation s\'effectuera dans une autre fenêtre.';
$string['vmoodlesource'] = 'Prendre les définitions VMoodle actives';

$string['configdisablemnetimportfilter_desc'] = '
Dans un moodle standard, la transmission par MNET des attributs de profil est limitée et filtrée. Dans un réseau augmenté,
il est nécessaire de désactiver ce filtrage pour que les profils soient intégralement synchronisés. Ce réglage active le
\'hack\' dans le module auth/mnet/auth.php. Ce comportement est naturellement supprimé dans l\'authentification multimnet
(réseau total).
';

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

$string['errorlocaladminconstrainted'] = 'Un administrateur local d\'un noeud virtuel ne peut
pas circuler à travers le réseau.';

$string['errorlocaladminconstrainted_help'] = 'Cette interdiction peut être levée dans les réglages généraux du bloc.
Ceci est le comportement par défaut pour éviter les confusions sur le compte "admin" dans un réseau de plates-formes.';

$string['configldapaccessattributes_desc'] = 'Une liste des attributs (séparés par une virgule) où des informations sur les sites
accessibles sont disponibles.';

$string['configldaphostpatterns_desc'] = 'Une liste d\'expression régulières (une par champ, séparées par des virgules), qui capturent (premier sous motif) la partie utile de l\'information d\'hote pour
l\'attribution des accès.';

$string['confighostwwwrootmask_desc'] = 'Un masque de génération du wwwroot que le bloc Mes hôtes du réseau peut reconnaître pour marquer le droit d\'accès.
Ce motif doit contenir un emplacement %HOSTINFO% qui recevra le résultat de l\'extraction de motif du contenu des champs ci-dessus.';
