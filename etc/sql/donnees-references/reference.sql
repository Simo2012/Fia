INSERT INTO LivraisonType VALUES (0, 'livraisons_aucun', false);
INSERT INTO LivraisonType VALUES (1, 'livraisons_retrait_immediat', true);
INSERT INTO LivraisonType VALUES (2, 'livraisons_retrait_differe', true);
INSERT INTO LivraisonType VALUES (3, 'livraisons_par_transporteur', true);
INSERT INTO LivraisonType VALUES (4, 'livraisons_par_magasin', true);
INSERT INTO LivraisonType VALUES (5, 'livraisons_colis_prive', true);

INSERT INTO SiteType VALUES(1, 'Site e-commerce');
INSERT INTO SiteType VALUES(2, 'Livreur');
INSERT INTO SiteType VALUES(3, 'Assurance');

INSERT INTO AdministrationType VALUES(1, 'Lien direct');
INSERT INTO AdministrationType VALUES(2, 'Via Certissim');
INSERT INTO AdministrationType VALUES(3, 'Flux XML');
INSERT INTO AdministrationType VALUES(4, 'CSV automatique');
INSERT INTO AdministrationType VALUES(5, 'CSV manuel');

INSERT INTO Package VALUES(1, 'Initial', true);
INSERT INTO Package VALUES(2, 'Essentiel', true);
INSERT INTO Package VALUES(3, 'Intégral', true);
INSERT INTO Package VALUES(4, 'Fidélité (Ancien payant)', true);
INSERT INTO Package VALUES(5, 'Historique (Ancien gratuit)', true);

INSERT INTO Option VALUES(1, 'Visualisations des données', '', true);
INSERT INTO Option VALUES(2, 'Extractions des données personnalisées', 'Téléchargement l''ensemble des données issue de l''extranet', true);
INSERT INTO Option VALUES(3, 'Bons plans', 'Encart publicitaire mettant en avant votre site ou un produit vendu sur votre site avec un code de réduction de type FIA-NET dans l''espace membre du site', true);
INSERT INTO Option VALUES(4, 'Newsletter', 'Encart publicitaire mettant en avant votre site ou un produit vendu sur votre site avec un code de réduction de type FIA-NET dans une newsletter mensuelle', true);
INSERT INTO Option VALUES(5, 'Réseaux sociaux', 'Page permettant de publier vos commentaires clients sur les réseaux sociaux (Facebook, Twitter,...) pendant une durée de 1 an', true);
INSERT INTO Option VALUES(6, 'Personnalisation des questionnaires', 'Possibilité d''ajouter des questions personnalisées au questionnaire après achat ou au questionnaire après livraison', true);
INSERT INTO Option VALUES(7, 'Personnalisation des e-mails', '', true);
INSERT INTO Option VALUES(8, 'Questionnaires en langues étrangères', '', true);
INSERT INTO Option VALUES(9, 'Publication des commentaires (version basique)', '', true);
INSERT INTO Option VALUES(10, 'Publication des commentaires (version expert)', 'Page permettant de personnaliser pendant une durée de 1 an votre flux XML contenant les commentaires de vos clients', true);
INSERT INTO Option VALUES(11, 'Relance questionnaire', '', true);
INSERT INTO Option VALUES(12, 'Google', '', true);
INSERT INTO Option VALUES(13, 'Avis produits', '', true);
INSERT INTO Option VALUES(14, 'Benchmark', '', true);

INSERT INTO OptionType VALUES(1, 'Option de base');
INSERT INTO OptionType VALUES(2, 'Option souscriptible');
INSERT INTO OptionType VALUES(3, 'Option non souscriptible');

INSERT INTO PackageOption VALUES(1, 2, 1, 2);
INSERT INTO PackageOption VALUES(2, 3, 1, 2);
INSERT INTO PackageOption VALUES(3, 4, 1, 2);
INSERT INTO PackageOption VALUES(4, 5, 1, 2);
INSERT INTO PackageOption VALUES(5, 10, 1, 2);
INSERT INTO PackageOption VALUES(6, 6, 1, 2);
INSERT INTO PackageOption VALUES(7, 1, 1, 2);
INSERT INTO PackageOption VALUES(8, 11, 1, 2);
INSERT INTO PackageOption VALUES(9, 12, 1, 1);
INSERT INTO PackageOption VALUES(10, 8, 1, 2);
INSERT INTO PackageOption VALUES(11, 7, 1, 2);
INSERT INTO PackageOption VALUES(12, 9, 1, 3);
INSERT INTO PackageOption VALUES(13, 14, 1, 2);
INSERT INTO PackageOption VALUES(14, 13, 1, 2);
INSERT INTO PackageOption VALUES(15, 2, 2, 2);
INSERT INTO PackageOption VALUES(16, 3, 2, 1);
INSERT INTO PackageOption VALUES(17, 4, 2, 2);
INSERT INTO PackageOption VALUES(18, 5, 2, 1);
INSERT INTO PackageOption VALUES(19, 10, 2, 2);
INSERT INTO PackageOption VALUES(20, 6, 2, 2);
INSERT INTO PackageOption VALUES(21, 1, 2, 1);
INSERT INTO PackageOption VALUES(22, 11, 2, 1);
INSERT INTO PackageOption VALUES(23, 12, 2, 1);
INSERT INTO PackageOption VALUES(24, 8, 2, 2);
INSERT INTO PackageOption VALUES(25, 7, 2, 2);
INSERT INTO PackageOption VALUES(26, 9, 2, 1);
INSERT INTO PackageOption VALUES(27, 14, 2, 2);
INSERT INTO PackageOption VALUES(28, 13, 2, 1);
INSERT INTO PackageOption VALUES(29, 2, 3, 1);
INSERT INTO PackageOption VALUES(30, 3, 3, 1);
INSERT INTO PackageOption VALUES(31, 4, 3, 2);
INSERT INTO PackageOption VALUES(32, 5, 3, 1);
INSERT INTO PackageOption VALUES(33, 10, 3, 1);
INSERT INTO PackageOption VALUES(34, 6, 3, 1);
INSERT INTO PackageOption VALUES(35, 1, 3, 1);
INSERT INTO PackageOption VALUES(36, 11, 3, 1);
INSERT INTO PackageOption VALUES(37, 12, 3, 1);
INSERT INTO PackageOption VALUES(38, 8, 3, 1);
INSERT INTO PackageOption VALUES(39, 7, 3, 1);
INSERT INTO PackageOption VALUES(40, 9, 3, 1);
INSERT INTO PackageOption VALUES(41, 14, 3, 1);
INSERT INTO PackageOption VALUES(42, 13, 3, 1);
INSERT INTO PackageOption VALUES(43, 2, 4, 2);
INSERT INTO PackageOption VALUES(44, 3, 4, 1);
INSERT INTO PackageOption VALUES(45, 4, 4, 2);
INSERT INTO PackageOption VALUES(46, 5, 4, 2);
INSERT INTO PackageOption VALUES(47, 10, 4, 2);
INSERT INTO PackageOption VALUES(48, 6, 4, 2);
INSERT INTO PackageOption VALUES(49, 1, 4, 1);
INSERT INTO PackageOption VALUES(50, 11, 4, 1);
INSERT INTO PackageOption VALUES(51, 12, 4, 1);
INSERT INTO PackageOption VALUES(52, 8, 4, 2);
INSERT INTO PackageOption VALUES(53, 7, 4, 2);
INSERT INTO PackageOption VALUES(54, 9, 4, 3);
INSERT INTO PackageOption VALUES(55, 14, 4, 2);
INSERT INTO PackageOption VALUES(56, 13, 4, 2);
INSERT INTO PackageOption VALUES(57, 2, 5, 2);
INSERT INTO PackageOption VALUES(58, 3, 5, 2);
INSERT INTO PackageOption VALUES(59, 4, 5, 2);
INSERT INTO PackageOption VALUES(60, 5, 5, 2);
INSERT INTO PackageOption VALUES(61, 10, 5, 2);
INSERT INTO PackageOption VALUES(62, 6, 5, 2);
INSERT INTO PackageOption VALUES(63, 1, 5, 1);
INSERT INTO PackageOption VALUES(64, 11, 5, 2);
INSERT INTO PackageOption VALUES(65, 12, 5, 1);
INSERT INTO PackageOption VALUES(66, 8, 5, 2);
INSERT INTO PackageOption VALUES(67, 7, 5, 2);
INSERT INTO PackageOption VALUES(68, 9, 5, 3);
INSERT INTO PackageOption VALUES(69, 14, 5, 2);
INSERT INTO PackageOption VALUES(70, 13, 5, 2);

INSERT INTO DelaiEnvoi VALUES(1, 0, 'delai_envoi_0_jour');
INSERT INTO DelaiEnvoi VALUES(2, 2, 'delai_envoi_2_jours');
INSERT INTO DelaiEnvoi VALUES(3, 3, 'delai_envoi_3_jours');
INSERT INTO DelaiEnvoi VALUES(4, 4, 'delai_envoi_4_jours');
INSERT INTO DelaiEnvoi VALUES(5, 5, 'delai_envoi_5_jours');
INSERT INTO DelaiEnvoi VALUES(6, 6, 'delai_envoi_6_jours');
INSERT INTO DelaiEnvoi VALUES(7, 7, 'delai_envoi_7_jours');
INSERT INTO DelaiEnvoi VALUES(8, 8, 'delai_envoi_8_jours');
INSERT INTO DelaiEnvoi VALUES(9, 9, 'delai_envoi_9_jours');
INSERT INTO DelaiEnvoi VALUES(10, 10, 'delai_envoi_10_jours');
INSERT INTO DelaiEnvoi VALUES(11, 11, 'delai_envoi_11_jours');
INSERT INTO DelaiEnvoi VALUES(12, 12, 'delai_envoi_12_jours');
INSERT INTO DelaiEnvoi VALUES(13, 13, 'delai_envoi_13_jours');
INSERT INTO DelaiEnvoi VALUES(14, 14, 'delai_envoi_14_jours');

INSERT INTO DelaiReception VALUES(1, 0, 'delai_reception_0_jour_imme');
INSERT INTO DelaiReception VALUES(2, 0, 'delai_reception_0_jour_aujour');
INSERT INTO DelaiReception VALUES(3, 1, 'delai_reception_1_jour');
INSERT INTO DelaiReception VALUES(4, 2, 'delai_reception_2_jours');
INSERT INTO DelaiReception VALUES(5, 3, 'delai_reception_3_jours');
INSERT INTO DelaiReception VALUES(6, 4, 'delai_reception_4_jours');
INSERT INTO DelaiReception VALUES(7, 5, 'delai_reception_5_jours');
INSERT INTO DelaiReception VALUES(8, 6, 'delai_reception_6_jours');
INSERT INTO DelaiReception VALUES(9, 7, 'delai_reception_7_jours');
INSERT INTO DelaiReception VALUES(10, 8, 'delai_reception_8_jours');
INSERT INTO DelaiReception VALUES(11, 9, 'delai_reception_9_jours');
INSERT INTO DelaiReception VALUES(12, 10, 'delai_reception_10_jours');
INSERT INTO DelaiReception VALUES(13, 11, 'delai_reception_11_jours');
INSERT INTO DelaiReception VALUES(14, 12, 'delai_reception_12_jours');
INSERT INTO DelaiReception VALUES(15, 13, 'delai_reception_13_jours');
INSERT INTO DelaiReception VALUES(16, 14, 'delai_reception_14_jours');
INSERT INTO DelaiReception VALUES(17, 15, 'delai_reception_15_jours');
INSERT INTO DelaiReception VALUES(18, 21, 'delai_reception_21_jours');
INSERT INTO DelaiReception VALUES(19, 28, 'delai_reception_28_jours');
INSERT INTO DelaiReception VALUES(20, 35, 'delai_reception_35_jours');
INSERT INTO DelaiReception VALUES(21, 42, 'delai_reception_42_jours');
INSERT INTO DelaiReception VALUES(22, 49, 'delai_reception_49_jours');
INSERT INTO DelaiReception VALUES(23, 61, 'delai_reception_61_jours');
INSERT INTO DelaiReception VALUES(24, 92, 'delai_reception_92_jours');
INSERT INTO DelaiReception VALUES(25, 122, 'delai_reception_122_jours');
INSERT INTO DelaiReception VALUES(26, 153, 'delai_reception_153_jours');
INSERT INTO DelaiReception VALUES(27, 183, 'delai_reception_183_jours');

INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(2, 'Questionnaire après livraison produit', '{"email": {"objet": "objet_email.2", "template": "livraison_produit", "expediteur": "suivi-livraison%''.06@fia-net.com", "expediteurRand": true}, "nbAvis": 29, "tombola": true, "livraison": true, "indicateur": {"type": "reponse_id", "vert": [58, 59], "jaune": [60], "rouge": [61], "question_id": 12}, "inscription": true, "noteGlobale": 27, "droitDeReponse": true, "recommandation": {"type": "note", "question_id": 13}, "questionsFiaNet": null, "libelleCommandeDate": "commun_commande_date", "nbJoursPourRepondre": 90, "commentairePrincipal": 14, "libelleQuestionnaireRepondu": "questionnaire_repondu.2"}', null, 7, 5);
INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(1, 'Questionnaire après achat produit', '{"email": {"objet": "objet_email.1", "template": "achat_produit", "expediteur": "suivi-livraison%''.06d@fia-net.com", "expediteurRand": true}, "nbAvis": 28, "tombola": true, "livraison": true, "indicateur": {"type": "reponse_id", "vert": [8, 9, 10], "jaune": [7], "rouge": [6], "question_id": 2}, "inscription": true, "noteGlobale": null, "droitDeReponse": true, "recommandation": null, "questionsFiaNet": [6], "libelleCommandeDate": "commun_commande_date", "nbJoursPourRepondre": 30, "commentairePrincipal": 3, "libelleQuestionnaireRepondu": "questionnaire_repondu.1"}', 2, 1, 1);
INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(4, 'Questionnaire après livraison service', '{}', null, 7, 5);
INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(3, 'Questionnaire après achat service', '{}', 3, 1, 1);
INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(5, 'Questionnaire unique produit', '{}', null, 1, 1);
INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(6, 'Questionnaire unique service', '{}', null, 1, 1);
INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(7, 'Questionnaire unique point relais', '{}', null, 1, 1);
INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(8, 'Questionnaire unique Aviva', '{}', null, 1, 1);
INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(9, 'Questionnaire unique livraison', '{}', null, 1, 1);
INSERT INTO QuestionnaireType (id, libelle, parametrage, questionnairetype_id, delaienvoi_id, delaireception_id) VALUES(10, 'Questionnaire light', '{"email": {"objet": "objet_email.10", "template": "sofinco", "expediteur": "no-reply@sofinco.fr", "expediteurRand": false}, "nbAvis": 666, "tombola": true, "livraison": false, "indicateur": {"type":"note", "question_id":19, "vert": {"min": 3.5, "max": 5}, "jaune": {"min": 2, "max": 3}, "rouge":{"min": 0.5, "max": 1.5}}, "inscription": true, "noteGlobale": 19, "droitDeReponse": false, "recommandation": null, "questionsFiaNet": null, "libelleCommandeDate": "commun_commande_date", "nbJoursPourRepondre": 90, "commentairePrincipal": 19, "libelleQuestionnaireRepondu": "questionnaire_repondu.5"}', null, 1, 1);
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 1, 'Product questionnaire after purchase');
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 2, 'Product questionnaire after delivery');
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 3, 'Service questionnaire after purchase');
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 4, 'Service questionnaire after delivery');
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 5, 'Unique product questionnaire');
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 6, 'Unique service questionnaire');
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 7, 'Unique questionnaire relay point');
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 8, 'Unique questionnaire Aviva');
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 9, 'Unique questionnaire delivery');
INSERT INTO QuestionnaireTypeTraduction (locale, object_class, field, foreign_key, content) VALUES('en', 'FIANET\SceauBundle\Entity\QuestionnaireType', 'libelle', 10, 'Light questionnaire');


INSERT INTO FluxStatut VALUES(1, 'A traiter');
INSERT INTO FluxStatut VALUES(2, 'En cours de traitement');
INSERT INTO FluxStatut VALUES(3, 'Traité et valide');
INSERT INTO FluxStatut VALUES(4, 'Traité et invalide');

INSERT INTO Langue VALUES(1, 'fr', 'langues_fr');
INSERT INTO Langue VALUES(2, 'en', 'langues_en');
INSERT INTO Langue VALUES(3, 'es', 'langues_es');
INSERT INTO Langue VALUES(4, 'de', 'langues_de');

INSERT INTO ExtranetMenuElement VALUES(1, null, 'accueil', 'accueil', 'extranet_accueil', 1, true, null);
INSERT INTO ExtranetMenuElement VALUES(2, null, 'questionnaires', 'questionnaires', 'extranet_questionnaires_questionnaires', 3, true, null);
INSERT INTO ExtranetMenuElement VALUES(3, null, 'questionnaires.questionnaires', 'questionnaires.questionnaires', 'extranet_questionnaires_questionnaires', 1, true, 2);
INSERT INTO ExtranetMenuElement VALUES(4, 6, 'questionnaires.questions_personnalisees', 'questionnaires.questions_personnalisees', 'extranet_questionnaires_questions_personnalisees', 2, true, 2);
INSERT INTO ExtranetMenuElement VALUES(5, 11, 'questionnaires.relance_questionnaires', 'questionnaires.relance_questionnaires', 'extranet_questionnaires_relance_questionnaires', 3, true, 2);

INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(1,1);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(1,2);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(1,10);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(2,1);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(2,2);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(2,10);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(3,1);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(3,2);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(3,10);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(4,1);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(4,2);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(4,10);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(5,1);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(5,2);
INSERT INTO ExtranetMenuElement_QuestionnaireType VALUES(5,10);

INSERT INTO MembreStatut (id, libelle) VALUES(1, 'Non confirmé');
INSERT INTO MembreStatut (id, libelle) VALUES(2, 'Confirmé');

INSERT INTO Civilite (id, libelle) VALUES (1, 'M.');
INSERT INTO Civilite (id, libelle) VALUES (2, 'Mme');

INSERT INTO RelanceStatut (id, libelle) VALUES (0, 'En cours de validation');
INSERT INTO RelanceStatut (id, libelle) VALUES (1, 'Validé');
INSERT INTO RelanceStatut (id, libelle) VALUES (2, 'Refusé');

-- TODO : à compléter
INSERT INTO QuestionType (id, libelle, personnalisable) VALUES (1, 'choix_unique', true); /* Choix unique */
INSERT INTO QuestionType (id, libelle, personnalisable) VALUES (2, 'choix_multiple', true); /* Choix multiple */
INSERT INTO QuestionType (id, libelle, personnalisable) VALUES (3, 'notation', true); /* Notation */
INSERT INTO QuestionType (id, libelle, personnalisable) VALUES (4, 'commentaire', true); /* Commentaire */
INSERT INTO QuestionType (id, libelle, personnalisable) VALUES (5, 'etoile', false); /* Etoile */
INSERT INTO QuestionType (id, libelle, personnalisable) VALUES (6, 'etoile_commentaire', false); /* Etoile + Commentaire */

INSERT INTO QuestionStatut (id, libelle) VALUES (0, 'Désactivée');
INSERT INTO QuestionStatut (id, libelle) VALUES (1, 'Activée');
INSERT INTO QuestionStatut (id, libelle) VALUES (2, 'En attente de validation');
INSERT INTO QuestionStatut (id, libelle) VALUES (3, 'Refusée');

INSERT INTO ReponseStatut (id, libelle) VALUES (0, 'Désactivée');
INSERT INTO ReponseStatut (id, libelle) VALUES (1, 'Activée');







