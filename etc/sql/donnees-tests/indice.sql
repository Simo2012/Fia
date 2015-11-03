-- Indices pour le questionnaire après achat
INSERT INTO Indice (id, indice_id, libelle, indicetype_id, question_id, reponse_id, questionnaireType_id)
VALUES
(1, null, 'Attributs du site', 1, null, null, 1),
(2, 1, 'Navigation', 1, 1, 1, 1),
(3, 1, 'Information sur les produits', 1, 1, 2, 1),
(4, 1, 'Prix des produits', 1, 1, 3, 1),
(5, 1, 'Facilité de commande', 1, 1, 4, 1),
(6, 1, 'Prix et options de transport', 1, 1, 5, 1),

(7, null, 'Les clients ont choisi ce site pour', 2, null, null, 1),
(8, 7, 'La nouveauté des produits', 2, 5, 15, 1),
(9, 7, 'Le prix des produits', 2, 5, 16, 1),
(10, 7, 'La disponibilité des produits', 2, 5, 17, 1),
(11, 7, 'Les promotions et les cadeaux', 2, 5, 18, 1),
(12, 7, 'Les offres proposées dans les newsletters', 2, 5, 19, 1),
(13, 7, 'Les informations sur les produits', 2, 5, 20, 1),
(14, 7, 'Les promotions sur le transport (réductions ou gratuité)', 2, 5, 21, 1),
(15, 7, 'La rapidité de transport', 2, 5, 22, 1),
(16, 7, 'La performance du site (recherche, personnalisation)', 2, 5, 23, 1),
(17, 7, 'La rapidité d''achat et de paiement (panier, ouverture d''un compte)', 2, 5, 24, 1),
(18, 7, 'Les facilités de paiement (crédit, prêt)', 2, 5, 25, 1),
(19, 7, 'La confiance que j''accorde à ce site', 2, 5, 26, 1),
(20, 7, 'J''ai l''habitude de faire mes courses sur ce site', 2, 5, 27, 1),
(21, 7, 'Autres', 2, 5, 28, 1),

(28, null, 'Nombre d''avis après achat', 3, null, null, 1)
;

-- Indices pour le questionnaire après livraison
INSERT INTO Indice (id, indice_id, libelle, indicetype_id, question_id, reponse_id, questionnaireType_id)
VALUES
(22, null, 'Intention de réachat', 2, null, null, 2),
(23, 22, 'Certainement', 2, 12, 58, 2),
(24, 22, 'Probablement', 2, 12, 59, 2),
(25, 22, 'Je ne sais pas', 2, 12, 60, 2),
(26, 22, 'Non, probablement pas', 2, 12, 61, 2),

(27, null, 'Indice de satisfaction FIA-NET', 1, 13, 62, 2),

(29, null, 'Nombre d''avis après livraison', 3, null, null, 2)
;