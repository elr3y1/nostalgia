-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-07-2025 a las 16:56:31
-- Versión del servidor: 10.3.39-MariaDB-cll-lve
-- Versión de PHP: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `elr3y_ejemplos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vik_app_games`
--

CREATE TABLE `vik_app_games` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `consola` varchar(100) DEFAULT NULL,
  `cover` varchar(1024) DEFAULT NULL,
  `disc` varchar(1024) DEFAULT NULL,
  `manual` varchar(1024) DEFAULT NULL,
  `logo` varchar(1024) DEFAULT NULL,
  `gameplay` varchar(1024) DEFAULT NULL,
  `soundtrack` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `vik_app_games`
--

INSERT INTO `vik_app_games` (`id`, `title`, `fileName`, `consola`, `cover`, `disc`, `manual`, `logo`, `gameplay`, `soundtrack`) VALUES
(1, 'ActRiser', 'https://archive.org/download/snesromsetcompleate/ActRaiser%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/ActRaiser(USA).webp.71a0e5796da6e47c596afb7e57c0fb47.webp', 'imgs/6334d71b-3753-4959-8c00-ff9690601611.webp', 'https://archive.org/download/snes-manual-archive/ActRaiser%20%28USA%29.pdf', 'imgs/702db1ac-e552-4f83-a1de-cac1ba847d57.webp', 'https://www.youtube.com/watch?v=f_rskSkXjZs', 'https://raw.githubusercontent.com/Valchrist23/Vic/refs/heads/main/gamesoundtracks/actriser.m3u'),
(2, 'ActRiser 2', 'https://archive.org/download/snesromsetcompleate/ActRaiser%202%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/ActRaiser2(USA).webp.41c95218b1c90861074615139f165fac.webp', 'imgs/2800b552-b939-4f1c-b912-690091d82174.webp', 'https://archive.org/download/snes-manual-archive/ActRaiser%202%20%28USA%29.pdf', 'imgs/f44d5639-037c-4015-ac64-064fd52acdf5.webp', 'https://www.youtube.com/watch?v=22SOTDNQvkI', NULL),
(3, 'Aladdin', 'https://archive.org/download/snesromsetcompleate/Aladdin%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/eef5d91c-960f-437a-a6dc-c26be0401682.webp', 'imgs/42c6f9e2-bcc4-40b9-93ab-43ef88d55cc2.webp', 'https://archive.org/download/snes-manual-archive/Disney%27s%20Aladdin%20%28USA%29.pdf', 'imgs/r2_4b8abc48-2b4b-460c-a201-c683ab19c6df.webp', 'https://www.youtube.com/watch?v=_inY8qT5UT4', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/aladdin.m3u'),
(4, 'Super Mario All Stars + Super Mario World', 'https://archive.org/download/snesromsetcompleate/Super%20Mario%20All-Stars%20%2B%20Super%20Mario%20World%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/d7cf5b14-875d-41f5-9bdd-94424f051aab.webp', 'imgs/301f53ec-1c4b-4ef7-a67e-4aea146a339c.webp', 'https://archive.org/download/snes-manual-archive/Super%20Mario%20All-Stars%20%2B%20Super%20Mario%20World%20%28USA%29.pdf', 'imgs/d46d249b-5d32-4761-b117-64125965bd4f.webp', NULL, NULL),
(5, 'Battletoads In Battlemaniacs', 'https://archive.org/download/snesromsetcompleate/Battletoads%20in%20Battlemaniacs%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/5ea74601-4b5c-446d-8bb6-d6e2caa59b5f.webp', 'imgs/99e80c2b-f5b8-477b-b491-d6c8c010a853.webp', 'https://archive.org/download/snes-manual-archive/Battletoads%20in%20Battlemaniacs%20%28USA%29.pdf', 'imgs/7634be74-4ddd-4f90-af6d-88c0c71e436b.webp', 'https://www.youtube.com/watch?v=WjzP6u6ayjc', 'https://raw.githubusercontent.com/Valchrist23/Vic/refs/heads/main/gamesoundtracks/battletoadsinbattlemaniacs.m3u'),
(6, 'Castlevania Dracula X', 'https://archive.org/download/snesromsetcompleate/Castlevania%20-%20Dracula%20X%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/a09bfab5-f4bc-49c1-bf38-228f71e8f1c2.webp', 'imgs/1022131630_Castlevania-DraculaX(USA).webp.06514d44659d877863d8980025a432fb.webp', 'https://archive.org/download/snes-manual-archive/Castlevania%20-%20Dracula%20X%20%28USA%29.pdf', 'imgs/58e08f8d-daa8-4069-b00a-d84f997609f5.webp', 'https://www.youtube.com/watch?v=fzCZuRS1LYM', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/castlevania_dracula_x.m3u'),
(7, 'Super Castlevania IV', 'https://archive.org/download/snesromsetcompleate/Super%20Castlevania%20IV%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/ad4f940a-3d1a-4ee5-86eb-8c75ce196eac.webp', 'imgs/77440fb4-f256-49e3-a8da-462b0a3cc21e.webp', 'https://archive.org/download/snes-manual-archive/Super%20Castlevania%20IV%20%28USA%29.pdf', 'imgs/b412a3d5-e582-4bfb-87d3-3b6e36199cc0.webp', 'https://www.youtube.com/watch?v=0fQ54I5N2kU', 'https://raw.githubusercontent.com/Valchrist23/Vic/refs/heads/main/gamesoundtracks/supercastlevaniaiv.m3u'),
(10, 'Castlevania', 'castlevania.nes', 'nes', 'imgs/ed029744-eff6-49f0-9f6c-361be31209b8.webp', 'imgs/7619e048-2eee-404b-992e-053976b7b1a7.webp', 'https://archive.org/download/NESManuals/Castlevania%20%28USA%29.pdf', 'imgs/940b085b-587b-4fd5-9101-62cc74908c60.webp', 'https://www.youtube.com/watch?v=gev1_Qwjze0', NULL),
(11, 'Mega Man', 'https://archive.org/download/nes-collection/Mega%20Man.zip', 'nes', 'imgs/333eb17c-04cc-4e6f-b4aa-4323c7c62691.webp', 'https://images.launchbox-app.com//8a2e01c6-ac9c-4986-a134-968f6a90a7ad.jpg', 'https://archive.org/download/NESManuals/Mega%20Man%20%28USA%29.pdf', 'imgs/245525e9-e179-4990-9474-14d02f345ca9.webp', 'https://www.youtube.com/watch?v=PxyLui5LdCc', NULL),
(16, 'Dino Crisis 2', '', 'playstation 1', 'imgs/a4ee3454-6951-44f4-a521-fa51162f3079.webp', 'imgs/9991fded-316d-4ec5-acb5-64031832108a.webp', 'https://archive.org/download/SonyPlaystationManuals/Dino%20Crisis%202%20%28USA%29.pdf', 'imgs/de7b56aa-e280-40bd-9aa4-78d5e2a2056b.webp', 'https://www.youtube.com/watch?v=vaEMpf-9Z7s', NULL),
(17, 'MediEvil 2', '', 'playstation 1', 'imgs/f12fc611-baf1-4bcd-8451-14a8d34b6473.webp', 'imgs/62609315-c2cc-4600-9d96-f319bdb3fb24.webp', 'https://archive.org/download/SonyPlaystationManuals/MediEvil%20II%20%28USA%29.pdf', 'imgs/2af2886a-95af-45e6-a9b8-6d9868c4da17.webp', 'https://www.youtube.com/watch?v=ATSxw2FSHdA', NULL),
(18, 'MediEvil', '', 'playstation 1', 'imgs/ed7b25f9-3f8a-4e66-a498-ee40748cf0a4.webp', 'imgs/1a9e3e62-91f9-4ca8-81d1-77a08b7a3c6c.webp', 'https://archive.org/download/SonyPlaystationManuals/MediEvil%20%28USA%29.pdf', 'imgs/03a1ce28-8120-4c12-9dc9-b9c007e850cb.webp', 'https://www.youtube.com/watch?v=sbKBbTqTeT8', NULL),
(19, 'Mega Man X4', '', 'playstation 1', 'imgs/24979c9a-5c43-4ed9-a1a0-3c64a4acfcb6.webp', 'imgs/f89e3fd5-2cfd-400d-bdcf-f3a9b470fa10.webp', 'https://archive.org/download/SonyPlaystationManuals/Mega%20Man%20X4%20%28USA%29.pdf', 'imgs/64c22e75-d686-4306-9619-8698c4acbb6b.webp', 'https://www.youtube.com/watch?v=Kkpf6f7J5Xc', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/megamanx4.m3u'),
(20, 'Breath Of Fire III', '', 'playstation 1', 'imgs/e064fc74-3530-4a33-b87c-f3b9c2e3778a.webp', 'imgs/a3d6b011-9a73-4bb1-b1f1-28aca699cfae.webp', 'https://ia802309.us.archive.org/24/items/all-ps1-rpg-manuals/Breath%20of%20Fire%20III_SP.pdf', 'imgs/7ac004cd-05f6-4f7b-a75d-502f04f6697c.webp', 'https://www.youtube.com/watch?v=2qJKRdJHnJU', NULL),
(21, 'Castlevania Symphony Of The Night', '', 'playstation 1', 'imgs/52066f45-52a0-4920-a636-63e15e942678.webp', 'imgs/d0c807f4-734d-40c4-8636-b1250b882eb2.webp', 'https://archive.org/download/SonyPlaystationManuals/Castlevania%20-%20Symphony%20of%20the%20Night%20%28USA%29.pdf', 'imgs/e0111472-3f37-441e-b925-3ca1dbce6e4a.webp', 'https://www.youtube.com/watch?v=Vke_g3-jgWU', 'https://raw.githubusercontent.com/Valchrist23/Vic/master/gamesoundtracks/castlevaniasotn.m3u'),
(22, 'Metal Gear Solid', '', 'playstation 1', 'imgs/5d3d25b2-7850-4b60-8446-491251a3b76c.webp', 'imgs/96aa0fcf-1afe-4215-b6dc-141e12b3cf3e.webp', 'https://archive.org/download/SonyPlaystationManuals/Metal%20Gear%20Solid%20%28USA%29%20%28Disc%201%29.pdf', 'imgs/r2_4afff946-6d09-4523-b99f-9791a6bcbc3d.webp', 'https://www.youtube.com/watch?v=vOcJK_SKiOc', 'https://raw.githubusercontent.com/Valchrist23/Vic/master/gamesoundtracks/metalgearsolidost.m3u'),
(23, 'Castlevania Legacy Of Darkness', '', 'n64', 'imgs/54c23e01-8a37-405a-a110-5a04466ba2ea.webp', 'imgs/4b990d9b-65ab-4374-bd23-6dc7686bb157.webp', 'https://archive.org/download/Nintendo64Manuals_201812/Castlevania%20-%20Legacy%20of%20Darkness%20%28USA%29.pdf', 'imgs/327edcac-ff85-4a8f-b7d9-d4f117688762.webp', 'https://www.youtube.com/watch?v=TjUMNVDOzNE', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/castlevania_legacy_of_darkness.m3u'),
(24, 'F-Zero X', '', 'n64', 'imgs/8134f65a-dfdf-4340-bd16-9fcb1c175c88.webp', 'imgs/6d22725f-a515-426a-8a3c-672f969bfb6f.webp', 'https://archive.org/download/Nintendo64Manuals_201812/F-Zero%20X%20%28USA%29.pdf', 'imgs/915929f3-3af0-4441-a45d-8a061f0a8a38.webp', 'https://www.youtube.com/watch?v=ugPX0W7sag0', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/fzerox.m3u'),
(25, 'Marvel VS Capcom 2 New Age Of Heroes', '', 'playstation 2', 'imgs/3428cdb3-eee2-4a09-8efc-73d8a27181ad.webp', 'imgs/5dc362af-5d1f-4a2b-afc8-362d3f6149a5.webp', 'https://archive.org/download/kirklands-manual-labor-sony-playstation-2-usa-4k-version/Marvel%20vs.%20Capcom%202%20%28USA%29.pdf', 'imgs/29b52036-3150-4eee-9cdf-f118cecab328.webp', 'https://www.youtube.com/watch?v=ORhz1Ql9Vl8', NULL),
(26, 'Castlevania Lament Of Innocence', '', 'playstation 2', 'imgs/1b0e65d4-9a5b-4957-acfc-058a47459c16.webp', 'imgs/747f4e55-e8d7-42ef-829d-00264de9fd9d.webp', 'https://archive.org/download/kirklands-manual-labor-sony-playstation-2-usa-4k-version/Castlevania-%20Lament%20of%20Innocence%20%28USA%29.pdf', 'imgs/9a4f9411-279d-48bb-815f-9abd42ba4306.webp', 'https://www.youtube.com/watch?v=Jnal7OwIN2k', NULL),
(27, 'Castlevania Curse Of Darkness', '', 'playstation 2', 'imgs/75c222ad-1040-4d0b-a878-5908ec5790f6.webp', 'imgs/cda546d6-b68c-493c-98e1-ca4491d88d41.webp', 'https://archive.org/download/kirklands-manual-labor-sony-playstation-2-usa-4k-version/Castlevania-%20Curse%20of%20Darkness%20%28USA%29.pdf', 'imgs/68280c8d-f717-42c1-a585-1c27d964190c.webp', 'https://www.youtube.com/watch?v=-0KP5Fvy44c', NULL),
(28, 'Metal Gear Solid 2 Substance', '', 'playstation 2', 'imgs/4fc1d592-f58f-4b65-b0ba-721b2fe35506.webp', 'imgs/d9ff68ae-7126-47e3-86c0-b6b2901720bf.webp', 'https://archive.org/download/kirklands-manual-labor-sony-playstation-2-usa-4k-version/Metal%20Gear%20Solid%202-%20Substance%20%28USA%29.pdf', 'imgs/ff45c599-2ef1-4c92-b73a-cc3dca7e0409.webp', 'https://www.youtube.com/watch?v=ooVavbXqOfE', NULL),
(29, 'Metal Gear Solid 3 Subsistance', '', 'playstation 2', 'imgs/fc75e3a8-b701-4532-85fa-e39e5e9f0487.webp', 'imgs/d83a8a4a-9b26-40cb-93c8-637ea4c819f7.webp', 'https://archive.org/download/kirklands-manual-labor-sony-playstation-2-usa-4k-version/Metal%20Gear%20Solid%203-%20Subsistence%20%28USA%29.pdf', 'imgs/afc3a292-1e70-4b3b-aa3f-6af0adcd5c47.webp', 'https://www.youtube.com/watch?v=srb_8SPx47c', NULL),
(30, 'The Prince Of Persia The Sands Of Time', '', 'playstation 2', 'imgs/32840ab6-64ee-4033-9513-710937d6a5e4.webp', 'imgs/828c4416-5345-465a-afd5-543dc438bb09.webp', 'https://archive.org/download/kirklands-manual-labor-sony-playstation-2-usa-4k-version/Prince%20of%20Persia-%20The%20Sands%20of%20Time%20%28USA%29.pdf', 'imgs/deb14a70-1b3e-47b1-a1a9-02939773097f.webp', 'https://www.youtube.com/watch?v=34s9b6sLMjk', NULL),
(31, 'The Prince Of Persia Warrior Within', '', 'playstation 2', 'imgs/79e2f6f0-2a49-44e9-875e-64dbfdc1cbcb.webp', 'imgs/2eced448-133c-432a-a2d4-15a09be53053.webp', 'https://archive.org/download/kirklands-manual-labor-sony-playstation-2-usa-4k-version/Prince%20of%20Persia-%20Warrior%20Within%20%28USA%29.pdf', 'imgs/cac7b0b3-12fa-4700-a69f-13c46635c95c.webp', 'https://www.youtube.com/watch?v=NZ4tGTbUwPs', NULL),
(32, 'The Prince Of Persia The Two Thrones', '', 'playstation 2', 'imgs/6f65453b-a2d3-4ebc-a083-c606adbec0b0.webp', 'imgs/91f98209-9d81-42b4-890b-8c960208116c.webp', 'https://archive.org/download/kirklands-manual-labor-sony-playstation-2-usa-4k-version/Prince%20of%20Persia-%20The%20Two%20Thrones%20%28USA%29.pdf', 'imgs/4cf5a70e-db46-4a7a-a02c-63740d210758.webp', 'https://www.youtube.com/watch?v=CuiCkfddORU', NULL),
(33, 'Super Mario Bros 3', 'https://archive.org/download/famicomnes-games/Super.Mario.Bros3%28J%29.zip', 'nes', 'imgs/174ffd17-c402-46f3-a298-da381f49bb23.webp', 'imgs/773d9360-52e3-45a9-b474-163b4c4b3eb6.webp', 'https://archive.org/download/NESManuals/Super%20Mario%20Bros.%203%20%28USA%29.pdf', 'imgs/237f73a1-7b42-4ded-af30-87f53a6d31fd.webp', 'https://www.youtube.com/watch?v=Na8rqq47gi0', 'https://raw.githubusercontent.com/Valchrist23/Vic/refs/heads/main/gamesoundtracks/supermario3.m3u'),
(34, 'Castlevania Rondo Of Blood', '', 'pc engine', 'imgs/ac61db5d-b611-4585-b08b-bb6fefe1898a.webp', 'imgs/b3c4fdc8-2f3b-4cfb-a23d-cc0b6946c310.webp', 'https://archive.org/download/akumajou-dracula-rondo-of-blood-manual/Akumajou%20Dracula%20-%20Rondo%20of%20Blood.pdf', 'imgs/aa96206f-a024-424d-b25f-144e78c7d276.webp', 'https://www.youtube.com/watch?v=ifP3Afn4HGA', 'https://raw.githubusercontent.com/Valchrist23/Vic/refs/heads/main/gamesoundtracks/castlevaniarondoofbloodpcengine.m3u'),
(35, 'Lords Of Thunder', '', 'pc engine', 'imgs/71939434-c016-41b6-a5e2-543cc4ea2e34.webp', 'imgs/ca12ae60-4487-4dfc-895c-d384b3410c7d.webp', 'https://archive.org/download/Nintendo64Manuals_201812/F-Zero%20X%20%28USA%29.pdf', 'imgs/2c536f64-a9e1-4d78-b597-9f6bc83e051e.webp', 'https://www.youtube.com/watch?v=fGoloG3NQqQ', 'https://raw.githubusercontent.com/Valchrist23/Vic/refs/heads/main/gamesoundtracks/lordsofthunderpcengine.m3u'),
(36, 'Chrono Trigger', '', 'snes', 'imgs/4f139083-5b08-4ecd-8e76-8f969cc5800c.webp', 'imgs/f47d60c4-ee4d-4b43-9d89-6f084a1b257f.webp', 'https://dn720005.ca.archive.org/0/items/kirklands_manual_labor_-_super_nintendo_-_usa_-_2k_version/Chrono%20Trigger%20%28USA%29.pdf', 'imgs/237701bb-472e-4b6c-81ae-f02805791430.webp', 'https://www.youtube.com/watch?v=649Nf8mWUHY', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/chrono_trigger.m3u'),
(37, 'Final Fantasy 3', '', 'snes', 'imgs/6ab27435-b728-42c6-882d-3518f246b0be.webp', 'imgs/2b24ffff-a3a1-4d33-9c3b-3c1081636a3e.webp', 'https://dn720005.ca.archive.org/0/items/kirklands_manual_labor_-_super_nintendo_-_usa_-_2k_version/Final%20Fantasy%20III%20%28USA%29.pdf', 'imgs/ab15c6b4-4479-4408-ae4a-4a8a88c38a77.webp', 'https://www.youtube.com/watch?v=enyY_mxUVVo', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/final_fantasy_vi.m3u'),
(38, 'Final Fantasy VII', '', 'playstation 1', 'imgs/70f12809-c886-4b8f-8821-f4070b6ae853.webp', 'imgs/b3bda860-0ef2-461e-b1ff-91869f22958e.webp', 'https://www.thealmightyguru.com/Wiki/images/f/fc/Final_Fantasy_VII_-_PS1_-_Manual.pdf', 'imgs/2aed1267-b7be-48ad-a5c4-9a27d29fd6ea.webp', 'https://www.youtube.com/watch?v=D6-lbtqQ7oY', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/final_fantasy_vii.m3u'),
(39, 'The Legend of Zelda: Ocarina of Time', '', 'n64', 'imgs/41c66c62-b72c-4df1-b536-b6d83aaf22ca.webp', 'imgs/a9db7030-68fd-4d8f-9ca4-66fe234915c2.webp', 'https://archive.org/download/manual-nintendo-64-the-legend-of-zelda-ocarina-of-time-en/Manual_Nintendo64_TheLegendOfZeldaOcarinaOfTime_EN.pdf', 'imgs/d3001c4b-9e23-43c5-97ea-2fe18df208e0.webp', 'https://www.youtube.com/watch?v=EfUoDfnH9ew', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/zelda_ocarina.m3u'),
(40, 'The Legend of Zelda: A Link to the Past', '', 'snes', 'imgs/b4d870f8-01aa-47b1-a984-fb26fecdd80f.webp', 'imgs/7de8570b-5750-4917-bc37-5df77e28cb97.webp', 'https://archive.org/download/clvpsaaee/CLV-P-SAAEE.pdf', 'imgs/357d463b-8d65-4fdd-9a4e-7fdcc408de8c.webp', 'https://www.youtube.com/watch?v=Dq_gUziNZUk', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/zelda_link_to_the_past.m3u'),
(41, 'The Legend of Zelda: Majora\'s Mask', '', 'n64', 'imgs/4830614f-1f9c-4f9a-bb21-41f0fdcc714f.webp', 'imgs/9813db05-c3c5-43c6-8c77-3241ab55eade.webp', 'https://archive.org/download/the-legend-of-zelda-majoras-mask-n64-manual/NZSE_E_11zon.pdf', 'imgs/d9219d94-9050-402c-a03d-6bea43258cc5.webp', 'https://www.youtube.com/watch?v=mQPE-fwnp-o', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/zelda_majora_mask.m3u'),
(42, 'Final Fantasy VIII', '', 'playstation 1', 'imgs/8c290977-637a-410f-9799-df9069155134.webp', 'imgs/15ba46a2-5f53-47b3-967e-2241b55fe70f.webp', 'https://archive.org/download/Final_Fantasy_VIII_1999_Squaresoft_US_SLUS-00892/Final_Fantasy_VIII_1999_Squaresoft_US_SLUS-00892.pdf', 'imgs/0a44847c-2097-4c8f-81cc-3e295ded8062.webp', 'https://www.youtube.com/watch?v=Qlvg_mGfm90', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/final_fantasy_viii.m3u'),
(43, 'Super Mario World', '', 'snes', 'imgs/26563ff1-d24d-487b-b651-5a71fbe59a8c.webp', 'imgs/abaf5d30-5c6c-4a0f-bc51-428180435b91.webp', 'https://ia600702.us.archive.org/27/items/SNESManuals/Super%20Mario%20World%20%28USA%29.pdf', 'imgs/8f62f6df-fca7-431d-8121-eb1e32e92deb.webp', 'https://www.youtube.com/watch?v=F9q20awtDIE', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/super_mario_world.m3u'),
(44, 'Super Mario 64', '', 'n64', 'imgs/72ae66ab-466a-4173-8e8d-0fb39ea1cfab.webp', 'imgs/ee156bd0-993d-44f3-bdaa-450727be1fe5.webp', 'https://archive.org/download/SuperMario64N64Manual/Super_Mario_64_-_N64_-_Manual.pdf', 'imgs/a865a58b-faf7-42e6-b0f5-be8e3837b3b0.webp', 'https://www.youtube.com/watch?v=Z3G4t6i5PAc', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/super_mario_64.m3u'),
(45, 'Chrono Cross', '', 'playstation 1', 'imgs/80508527-7a17-4e76-b344-aaf5cd97e0f8.webp', 'imgs/c7dea940-132f-4b0b-846a-ab47e5577332.webp', 'https://archive.org/download/all-ps1-rpg-manuals/Chrono%20Cross_SP.pdf', 'imgs/8816dc8a-a8bb-48ed-90ed-f0b2ec5ac93f.webp', 'https://www.youtube.com/watch?v=HnWo7eIbTWg', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/chrono_cross.m3u'),
(46, 'Mega Man X', 'https://archive.org/download/snesromsetcompleate/Mega%20Man%20X%20%28U%29%20%28V1.1%29%20%5B%21%5D.zip', 'snes', 'imgs/2de2829b-33ae-4ca7-b85a-2cf6469a96f3.webp', 'imgs/0eff5fe5-c943-46c1-a218-10705bbcab57.webp', 'https://archive.org/download/mega-man-x-snes-manual/CLV-P-SABCE.pdf', 'imgs/r2_e5c682ea-a0bd-4c93-b923-2e70fcdeb77b.webp', 'https://www.youtube.com/watch?v=0qMDQTF8uVQ', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/megamanx.m3u'),
(47, 'Illusion of Gaia', 'https://archive.org/download/snesromsetcompleate/Illusion%20of%20Gaia%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/178ab20d-d219-4078-b27e-9c1e98287701.webp', 'imgs/025df42c-8bd6-430d-85af-00561663de9a.webp', 'https://archive.org/download/snes_Illusion_of_Gaia_USA/Illusion_of_Gaia_USA.pdf', 'imgs/5380b615-c8a5-402d-8693-496991688bc9.webp', 'https://www.youtube.com/watch?v=obmIQsY07gY', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/illusion_of_gaia.m3u'),
(48, 'Donkey Kong Country', 'https://archive.org/download/snesromsetcompleate/Aladdin%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/d6962065-6d40-4eab-b229-863fa10d4ea0.webp', 'imgs/407aa3b9-759a-401f-93ea-1b8d4d3f550b.webp', 'https://archive.org/download/donkey-kong-country-snes-manual/491-donkey-kong-country-super-nintendo-manual-usa.pdf', 'imgs/950d5d48-2a19-4750-8423-ec61caad5955.webp', 'https://www.youtube.com/watch?v=_Eug8PRZWlc', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/donkey_kong_country.m3u'),
(49, 'Super Mario RPG: Legend of the Seven Stars', 'https://archive.org/download/snesromsetcompleate/Super%20Mario%20RPG%20-%20Legend%20of%20the%20Seven%20Stars%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/0d4ab2c3-63a0-4a5e-93b8-25ea9d2ac34d.webp', 'imgs/6e1c5b65-8ae4-48bf-bea6-37162baa2d2b.webp', 'https://dn720005.ca.archive.org/0/items/kirklands_manual_labor_-_super_nintendo_-_usa_-_2k_version/Super%20Mario%20RPG%20-%20Legend%20of%20the%20Seven%20Stars%20%28USA%29.pdf', 'imgs/f8ef3398-f759-4d00-acaa-128a862fe977.webp', 'https://www.youtube.com/watch?v=D0LW6IPaVxs', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/mariorpg.m3u'),
(50, 'Secret of Mana', 'https://archive.org/download/snesromsetcompleate/Secret%20of%20Mana%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/3cfd35b6-a868-43c1-a934-38782cf496da.webp', 'imgs/52b1f293-bf3e-4d89-8fad-5d4e27146c7e.webp', 'https://archive.org/download/secret-of-mana-usa-hq/Secret%20of%20Mana%20%28%20USA%20%29%20HQ.pdf', 'imgs/61d1966b-5614-4f9a-9c2d-0cb53b884f3d.webp', 'https://www.youtube.com/watch?v=NMklmFM5IWU', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/secret_of_mana.m3u'),
(51, 'Banjo-Kazooie', '', 'n64', 'imgs/96e9a938-d3e3-4cd4-a515-187682b69694.webp', 'imgs/ecbb2097-9308-4a49-b49f-485446365c4d.webp', 'https://archive.org/download/banjo-kazooie-n64-manual/Banjo-Kazooie_-_Nintendo_64_-_Manual.pdf', 'imgs/e508fa77-2402-4c5f-a3e0-545b8860b8fe.webp', 'https://www.youtube.com/watch?v=qP1-PiYhpK0', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/banjo_kazooie.m3u'),
(52, 'Star Fox 64', '', 'n64', 'imgs/2f1a6629-5c8b-4964-84d2-58da357e5f98.webp', 'imgs/30522d50-9622-4de3-a700-ff3e36052345.webp', 'https://dn720701.ca.archive.org/0/items/Nintendo64GameManuals/StarFox64u.pdf', 'imgs/16cdd370-4448-41c5-8219-591e2985bb60.webp', 'https://www.youtube.com/watch?v=GhQp8le67Xo', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/starfox64.m3u'),
(53, 'GoldenEye 007', '', 'n64', 'imgs/989cf647-c8de-40c8-b7f8-47b1ba219439.webp', 'imgs/93a43e66-0d0c-42d3-9126-da2a0548533d.webp', 'https://archive.org/download/goldeneye-007-n-64/Goldeneye%20007%20%28N64%29.pdf', 'imgs/f085e96a-f102-4ae1-aa32-90e34cf5c210.webp', 'https://www.youtube.com/watch?v=Z5oTkVsTZdI', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/goldeneye.m3u'),
(54, 'F-Zero', 'https://archive.org/download/snesromsetcompleate/F-ZERO%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/60bdcf0b-0d4d-40fb-ba46-c24c0a025506.webp', 'imgs/029ba34b-d0c8-4eed-9a4a-ecc0f14de489.webp', 'https://archive.org/download/f-zero-usa/F-Zero%20%28%20USA%20%29.pdf', 'imgs/bd19ea8c-348e-4887-8383-7c4227948e72.webp', 'https://www.youtube.com/watch?v=rOguzV-CW4s', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/fzero.m3u'),
(55, 'Street Fighter II', 'https://archive.org/download/snesromsetcompleate/Street%20Fighter%20II%20-%20The%20World%20Warrior%20%28U%29%20%5B%21%5D.zip', 'snes', 'imgs/10730576-2800-45f1-ad1d-eaea8af58984.webp', 'imgs/0f75f593-6df6-4366-a4ef-bb4aa9762ff4.webp', 'https://archive.org/download/street-fighter-ii-usa/Street%20Fighter%20II%20%28USA%29.pdf', 'imgs/17463b37-ecf8-4629-9794-94a3d472e025.webp', 'https://www.youtube.com/watch?v=ol6EOgu89l0', 'https://raw.githubusercontent.com/elr3y1/vik_app_games/refs/heads/main/soundtracks/street_fighter_2.m3u');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `vik_app_games`
--
ALTER TABLE `vik_app_games`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `vik_app_games`
--
ALTER TABLE `vik_app_games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
