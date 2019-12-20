USE real_time_chat;

#テーブルを参照
select * from users;
select * from talk_logs;
select * from talk_rooms;
select * from talk_room_members;

#メンバーが誰も居ない部屋を取得
DELETE FROM talk_rooms
	WHERE(
		SELECT DISTINCT talk_rooms.room_id FROM talk_rooms 
        LEFT JOIN talk_room_members ON talk_rooms.room_id = talk_room_members.room_id 
        WHERE talk_room_members.room_id IS null
	);
#部屋にユーザーが居れば0、居なければ1を返す。
SELECT count(*) FROM talk_rooms
	LEFT JOIN talk_room_members ON talk_rooms.room_id = talk_room_members.room_id
    WHERE talk_rooms.room_id = 1 AND talk_room_members.room_id IS null;
    
#ユーザーを削除、auto_incrementをリセット
DELETE FROM users WHERE user_id = 3;
ALTER TABLE users auto_increment = 3; 

#talk_logsの削除、room_memberの削除、talk_roomを削除、auto_incrementをリセット
DELETE FROM talk_logs WHERE room_id > 1; 
DELETE FROM talk_room_members WHERE room_id>1;
DELETE FROM talk_rooms WHERE room_id > 1;
ALTER TABLE talk_rooms auto_increment = 1; 

#全てのtalk_logsを削除、auto_incrementをリセット
delete from talk_logs where talk_id > 0; 
ALTER TABLE talk_logs auto_increment = 1;

#新規ルーム作成 
INSERT talk_rooms VALUES (null,'ルーム名');

#ログインIDからuser_idを特定する。
SELECT user_id FROM users WHERE login_id = 'onna';

#ルームメンバーを追加
INSERT talk_room_members VALUES(1, 1);

#user_idが参加してるルームの一覧を取得する。
SELECT talk_rooms.room_id,talk_rooms.room_name FROM talk_rooms JOIN talk_room_members ON talk_rooms.room_id = talk_room_members.room_id WHERE user_id = 1;

#user_idがroom_idに参加しているかどうか取得する。
SELECT COUNT(*) FROM talk_rooms JOIN talk_room_members ON talk_rooms.room_id = talk_room_members.room_id WHERE user_id = 1 AND talk_rooms.room_id = 1;

#room_idのtalk_logsと、その他必要な情報(user_nameとuser_id)を、talk_time順に取得する。
SELECT DISTINCT talk_id,talk_value,talk_time,user_name,users.user_id 
	FROM talk_logs 
	JOIN talk_room_members 
    ON talk_logs.room_id = talk_room_members.room_id 
	JOIN users
    ON talk_logs.user_id = users.user_id 
    WHERE talk_logs.room_id = 1 
    ORDER BY talk_time;