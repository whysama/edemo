-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 18 Décembre 2014 à 18:11
-- Version du serveur :  5.5.35-0ubuntu0.12.04.2
-- Version de PHP :  5.5.18-1+deb.sury.org~precise+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `easyDesign`
--

-- --------------------------------------------------------

--
-- Structure de la table `model`
--

CREATE TABLE IF NOT EXISTS `model` (
`id_model` int(11) NOT NULL,
  `model_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `model_description` text COLLATE utf8_bin,
  `model_image` varchar(128) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Contenu de la table `model`
--

INSERT INTO `model` (`id_model`, `model_name`, `model_description`, `model_image`) VALUES
(1, 'Musee', 'A model designed for our museum application', '');

-- --------------------------------------------------------

--
-- Structure de la table `pattern`
--

CREATE TABLE IF NOT EXISTS `pattern` (
`id_pattern` int(11) NOT NULL,
  `id_model` int(11) NOT NULL,
  `pattern_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `pattern_description` text COLLATE utf8_bin,
  `pattern_detail` text COLLATE utf8_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
`id_project` int(11) NOT NULL,
  `id_model` int(11) NOT NULL,
  `id_creator` int(11) NOT NULL,
  `project_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `project_description` text COLLATE utf8_bin
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `project_auth`
--

CREATE TABLE IF NOT EXISTS `project_auth` (
  `id_project` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `project_page`
--

CREATE TABLE IF NOT EXISTS `project_page` (
`id_page` int(11) NOT NULL,
  `id_project` int(11) NOT NULL,
  `id_pattern` int(11) NOT NULL,
  `page_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `page_description` text COLLATE utf8_bin,
  `page_detail` text COLLATE utf8_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id_user` int(11) NOT NULL,
  `login` varchar(64) COLLATE utf8_bin NOT NULL,
  `password` varchar(64) COLLATE utf8_bin NOT NULL,
  `user_type` tinyint(1) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id_user`, `login`, `password`, `user_type`) VALUES
(1, 'airweb', '856bba1e6e899f629b031a9d4f846378f901bc52', 1),
(2, 'sfr', '856bba1e6e899f629b031a9d4f846378f901bc52', 2);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `model`
--
ALTER TABLE `model`
 ADD PRIMARY KEY (`id_model`);

--
-- Index pour la table `pattern`
--
ALTER TABLE `pattern`
 ADD PRIMARY KEY (`id_pattern`,`id_model`);

--
-- Index pour la table `project`
--
ALTER TABLE `project`
 ADD PRIMARY KEY (`id_project`,`id_model`,`id_creator`);

--
-- Index pour la table `project_auth`
--
ALTER TABLE `project_auth`
 ADD PRIMARY KEY (`id_project`,`id_user`);

--
-- Index pour la table `project_page`
--
ALTER TABLE `project_page`
 ADD PRIMARY KEY (`id_page`,`id_project`,`id_pattern`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id_user`), ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `model`
--
ALTER TABLE `model`
MODIFY `id_model` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `pattern`
--
ALTER TABLE `pattern`
MODIFY `id_pattern` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `project`
--
ALTER TABLE `project`
MODIFY `id_project` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `project_page`
--
ALTER TABLE `project_page`
MODIFY `id_page` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
