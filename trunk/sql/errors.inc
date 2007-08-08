2007.08.07 20:48 #1064
SELECT groups.access, players.name, players.level, players.maglevel, players.experience FROM players FULL OUTER JOIN groups ON players.group_id = groups.id ORDER BY level DESC LIMIT 0, 50;
You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'OUTER JOIN groups ON players.group_id = groups.id ORDER BY level DESC LIMIT 0, 5' at line 1
2007.08.07 20:52 #1054
SELECT groups.access, players.name, player_skills.value FROM players, player_skills LEFT OUTER JOIN groups ON players.group_id = groups.id WHERE players.id = player_skills.player_id AND player_skills.skillid = 3 ORDER BY `value` DESC LIMIT 0, 50;
Unknown column 'players.group_id' in 'on clause'
