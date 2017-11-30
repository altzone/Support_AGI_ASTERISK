--
-- Base de données :  `support`
--

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `soc` varchar(60) NOT NULL,
  `contrat` int(11) NOT NULL,
  `h_min` int(11) NOT NULL,
  `h_max` int(11) NOT NULL,
  `day` int(1) NOT NULL,
  `panne` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `id_cust` int(11) DEFAULT NULL,
  `num_contrat` int(5) DEFAULT NULL,
  `clid` varchar(15) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `urgence` tinyint(1) DEFAULT NULL,
  `hno` tinyint(1) DEFAULT NULL,
  `panne` tinyint(1) DEFAULT NULL,
  `record` varchar(80) DEFAULT NULL,
  `message` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `num_tel`
--

CREATE TABLE `num_tel` (
  `id` int(11) NOT NULL,
  `num_contrat` int(11) NOT NULL,
  `id_cust` int(11) NOT NULL,
  `tel` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `urgence`
--

CREATE TABLE `urgence` (
  `id` int(11) NOT NULL,
  `contrat` int(11) NOT NULL,
  `callerid` varchar(20) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `num_tel`
--
ALTER TABLE `num_tel`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `urgence`
--
ALTER TABLE `urgence`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `num_tel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `urgence`
--
ALTER TABLE `urgence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
