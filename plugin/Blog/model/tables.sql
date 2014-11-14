-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 27 Août 2014 à 02:09
-- Version du serveur :  5.5.31-1~dotdeb.0-log
-- Version de PHP :  5.4.30-1~dotdeb.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `dev`
--

-- --------------------------------------------------------

--
-- Structure de la table `cms_blog_comments`
--

CREATE TABLE IF NOT EXISTS `cms_blog_comments` (
  `id` int(16) NOT NULL,
  `type` varchar(16) NOT NULL,
  `author_id` int(16) NOT NULL,
  `post_id` int(16) NOT NULL,
  `content` text NOT NULL,
  `state` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `cms_blog_posts`
--

CREATE TABLE IF NOT EXISTS `cms_blog_posts` (
  `id` int(16) NOT NULL,
  `title` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(16) NOT NULL,
  `date` int(16) NOT NULL,
  `state` int(8) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
