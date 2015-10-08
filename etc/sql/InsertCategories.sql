insert into categorie (id,categorie_id, libelle, libellecourt, slug, actif, accueil) 
VALUES 
(1, null, 'Alimentation & gastronomie', 'Alimentation', 'alimentation-gastronomie', true, true), 
(302, 1, 'Chocolats & confiserie', 'Chocolats & confiserie', 'chocolats-confiserie', true, true),
(305, 1, 'Vins & spiritueux', 'Vins', 'vins-spiritueux', true, true), 
(304, 1, 'Produits du terroir', 'Terroir', 'produits-du-terroir', true, true),

(2, null, 'Auto & moto', 'Auto & moto', 'auto-moto', true, true),
(307, 2, 'Accessoires', 'Accessoires', 'accessoires', true, true),
(234, 2, 'Pièces détachées & Tuning', 'Pièces detachées', 'pieces-detachees-et-tuning', true, true),

(19, null, 'Bébés & enfants', 'Bébés & enfants', 'bebes-et-enfants', true, true),
(257, 19, 'Jeux & jouets d''éveil', 'Eveil', 'jeux-et-jouets-d-eveil', true, true),
(258, 19, 'Puériculture', 'Puériculture', 'puericulture', true, true),
(259, 19, 'Mobilier bébés', 'Mobilier', 'mobilier-bebes', true, true),
(272, 19, 'Mode bébés & enfants', 'Mode', 'mode-bebes-et-enfants', true, true),

(3, null, 'Culture & divertissements', 'Culture', 'culture-divertissements', true, true),
(313, 3, 'Jeux & jouets', 'Jouets', 'jeux-jouets', true, true),
(316, 3, 'Musique', 'Musique', 'musique', true, true),
(246, 3, 'Vidéo DVD & Blu Ray', 'DVD Blu Ray', 'video-dvd-et-blu-ray', true, true),

(5, null, 'Electroménager', 'Electroménager', 'electromenager', true, true),
(324, 5, 'Congélateurs', 'Congélateurs', 'congelateurs', true, true),
(326, 5, 'Micro-ondes', 'Micro-ondes', 'micro-onde', true, true),
(103, 5, 'Lave-linges', 'Lave-linge', 'lave-linges', true, true),

(7, null, 'Fleurs & cadeaux', 'Fleurs & cadeaux', 'fleurs-cadeaux', true, true),
(267, 7, 'Cartes & Papeterie', 'Cartes', 'cartes-et-papeterie', true, true),
(333, 7, 'Service d''aide pour les cadeaux', 'Service d''aide', 'service-d-aide-pour-les-cadeaux', true, true),
(335, 7, 'Fleurs', 'Fleurs', 'fleurs', true, true),
(336, 7, 'Souvenirs', 'Souvenirs', 'souvenirs', true, true),

(15, null, 'Hifi, photo & vidéos', 'Hifi, photo & vidéos', 'hifi-photo-videos', true, true),
(146, 15, 'Appareils photo', 'Photo', 'appareils-photo', true, true),
(148, 15, 'Téléviseurs', 'Téléviseurs', 'televiseurs', true, true),
(198, 15, 'Lecteurs DVD & Blu Ray', 'Lecteurs DVD', 'lecteurs-dvd-et-blu-ray', true, true),

(8, null, 'Informatique & logiciels', 'Informatique', 'informatique-logiciels', true, true),
(340, 8, 'Périphériques', 'Périphériques', 'peripheriques', true, true),
(113, 8, 'Imprimantes', 'Imprimantes', 'imprimantes', true, true),
(213, 8, 'Ordinateurs PC & Mac', 'Ordinateurs', 'ordinateurs-fixes', true, true),

(4, null, 'Maison & jardin', 'Maison & jardin', 'maison-jardin', true, true),
(320, 4, 'Jardinage', 'Jardinage', 'jardinage', true, true),
(321, 4, 'Fournitures & mobiliers', 'Mobiliers', 'fournitures-mobiliers', true, true),
(394, 4, 'Décoration', 'Déco', 'decoration', true, true),
(399, 4, 'Autres', 'Autres', 'autres', true, true),
(227, 4, 'Bricolage & Outillage', 'Bricolage', 'bricolage-et-outillage', true, true),


(9, null, 'Santé & beauté', 'Santé & beauté', 'sante-beaute', true, true),
(239, 9, 'Produits diététiques & bien-être', 'Produits diététiques', 'produits-dietetiques-et-bien-etre', true, true),
(240, 9, 'Cosmétiques & cosmétiques bio', 'Cosmétiques', 'cosmetiques-et-cosmetiques-bio' , true, true),
(241, 9, 'Soins du corps & du visage', 'Soins du corps', 'soins-du-corps-et-du-visage', true, true)

(11, null, 'Services aux professionnels', 'Serv. Professionnels', 'services-aux-professionnels', true, true),
(59, 11, 'Divers', 'Divers', 'divers', true, true),
(63, 11, 'Prestataire internet', 'Prestataire internet', 'prestataire-internet', true, true),

(12, null, 'Sport', 'Sport', 'sport', true, true),
(66, 12, 'Matériel', 'Matériel', 'materiel', true, true),
(68, 12, 'Vêtements', 'Vêtements', 'vetements', true, true),
(247, 12, 'Fitness & Diététique', 'Prestataire internet', 'pFitness', true, true),

(16, null, 'Téléphonie & communication', 'Téléphonie & communication', 'telephones-fixes', true, true),
(127, 16, 'Accessoires', 'Accessoires', 'accessoires', true, true),
(128, 16, 'Téléphones fixes', 'Téléphones fixes', 'telephones-fixes', true, true),
(129, 16, 'Téléphones mobiles', 'Mobiles', 'telephones-mobiles', true, true),

(17, null, 'Vie pratique', 'Vie pratique', 'vie-pratique', true, true),
(250, 17, 'Faire-part & cartes de voeux', 'Faire-part', 'faire-part-et-cartes-de-voeux', true, true),
(282, 17, 'Code de la route', 'Code de la route', 'Code de la route', true, true),

(14, null, 'Voyage & tourisme', 'Voyage & tourisme', 'voyage-tourisme', true, true),
(131, 14, 'Billets d''avion', 'Billets d''avion', 'billets-d-avion', true, true),
(134, 14, 'Séjours', 'Séjours', 'sejours', true, true),
(138, 14, 'Week-ends', 'Week-ends', 'week-ends', true, true),

(13, null, 'Vêtements & accessoires', 'Vêtements', 'vetements-accessoires', true, true),
(75, 13, 'Mode femme', 'Mode femme', 'mode-femme', true, true),
(76, 13, 'Mode homme', 'Mode homme', 'mode-homme', true, true),
(194, 13, 'Accessoires de mode', 'Accessoires', 'accessoires-de-mode', true, true);
