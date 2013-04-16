
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `menu_top` (
  `name` text COLLATE utf8_unicode_ci,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` text COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

INSERT INTO `menu_top` (`name`, `id`, `link`, `sort_order`, `access`) VALUES
('Admin', 9, '$RFS_SITE_URL/admin/adm.php', 434, 255),
('News', 10, 'index.php', 124, 0),
('Videos', 11, '$RFS_SITE_URL/modules/videos/videos.php', 195, 0),
('Forum', 12, '$RFS_SITE_URL/modules/forums/forums.php?forum_list=yes', 199, 0),
('Pictures', 13, '$RFS_SITE_URL/modules/pictures/pics.php', 189, 0),
('Profile', 14, '$RFS_SITE_URL/modules/profile/profile.php', 432, 0),
('Wiki', 15, '$RFS_SITE_URL/modules/wiki/rfswiki.php', 140, 0),
('Comics', 16, '$RFS_SITE_URL/modules/comics/comics.php', 155, 0),
('Meme Generator', 17, '$RFS_SITE_URL/modules/pictures/pics.php?action=showmemes', 192, 0),
('Video Wall', 18, '$RFS_SITE_URL/modules/video_wall/v.php', 196, 0);
