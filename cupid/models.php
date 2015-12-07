<?php

class Conexion{
	private static $conexion;
	private static $log;
	public static function getConexion(){
		if(!isset(self::$conexion)){
			self::$conexion = new mysqli("idegard.com", "chinook", "chinook", "chinook");
		}
		return self::$conexion;
	}
}

class Track{
	public function __construct($a,$b,$d,$e,$f,$g,$h,$i){
			$this->TrackId=$a;
			$this->Name=$b;
			$this->Composer=$d;
			$this->Milliseconds=$e;
			$this->Bytes=$f;
			$this->UnitPrice=$g;
			$this->Album=$h;
			$this->updated_at=$i;
	}
	
	public static function all($limit = 20, $offset = 0, $OB = "TrackId", $OD = "ASC"){
		return self::get(null, null, $limit, $offset, $OB, $OD);
	}
	
	public static function find($id){
		return self::get($id, null, 1, 0, "TrackId", "ASC")[0];
	}
	
	public static function like($like, $limit = 50, $offset = 0, $OB = "TrackId", $OD = "ASC"){
		return self::get(null, $like, $limit, $offset, $OB, $OD);
	}
	
	public static function create($name,$composer,$milliseconds,$bytes,$unitPrice,$album){
		return new self(null,$name,$composer,$milliseconds,$bytes,$unitPrice,(is_int($album)? $album : $album->AlbumId),null);
	}
	
	public function save(){
		if(!is_null($this->TrackId)){
			$sQuery = 	"UPDATE track SET ".
						"Name='$this->Name', Composer='$this->Composer', ".
						"Milliseconds=$this->Milliseconds, Bytes=$this->Bytes, ".
						"UnitPrice=$this->UnitPrice, updated_at=NOW(), AlbumId=".
						(is_int($this->Album)? $this->Album : $this->Album->AlbumId)." ".
						"WHERE track.TrackId = $this->TrackId";
			return Conexion::getConexion()->query($sQuery);
		}else{
			$sQuery = "INSERT INTO track VALUES(null, '$this->Name', $this->Album, 1, 1, '$this->Composer', $this->Milliseconds, $this->Bytes, $this->UnitPrice, NOW())";
			if(Conexion::getConexion()->query($sQuery)){
				$new_object = self::find(Conexion::getConexion()->insert_id);
				$this->TrackId = $new_object->TrackId;
				$this->Album = $new_object->Album;
				$this->updated_at = $new_object->updated_at;

				return $this;		
			}else{
				return false;
			}
			echo $sQuery;
		}
	}
	
	public function delete(){
		$sQuery = "DELETE FROM track WHERE track.TrackId = $this->TrackId";
		return Conexion::getConexion()->query($sQuery);
	}

	private static function get($id, $like, $limit, $offset, $OB, $OD){
		$busqueda = isset($_GET['b'])? $_GET['b'] : "";

		$sQuery = "SELECT track.TrackId AS 'track.TrackId', ".
					"track.Name AS 'track.Name', ".
					"track.AlbumId AS 'track.AlbumId', ".
					"track.Composer AS 'track.Composer', ".
					"track.Milliseconds AS 'track.Milliseconds', ".
					"track.Bytes AS 'track.Bytes', ".
					"track.UnitPrice AS 'track.UnitPrice', ".
					"track.updated_at AS 'track.updated_at', ".
					"album.AlbumId AS 'album.AlbumId', ".
					"album.Title AS 'album.Title', ".
					"album.ArtistId AS 'album.ArtistId', ".
					"album.updated_at AS 'album.updated_at', ".
					"artist.ArtistId As 'artist.ArtistId', ".
					"artist.Name As 'artist.Name', ".
					"artist.updated_at As 'artist.updated_at' ".
					"FROM track INNER JOIN album ON track.AlbumId = album.AlbumId INNER JOIN artist ON album.ArtistId = artist.ArtistId ".
					(!is_null($id)? "WHERE track.TrackId = $id " : "").
					(!is_null($busqueda)? "WHERE track.Name like '%$busqueda%' " : "No hay resultados").
					"ORDER BY $OB $OD LIMIT $limit OFFSET $offset";		

		$query = Conexion::getConexion()->query($sQuery);
		
		while($track = $query->fetch_assoc())
			
			$tracks[] = new self($track['track.TrackId'],$track['track.Name'],$track['track.Composer'],
								$track['track.Milliseconds'],$track['track.Bytes'],$track['track.UnitPrice'],
								new Album($track['album.AlbumId'],$track['album.Title'],
									new Artist($track['artist.ArtistId'],$track['artist.Name'],$track['artist.updated_at']),
										$track['album.updated_at']),
								$track['track.updated_at']);

		if(isset($tracks)){
			return $tracks;
		}else{
			echo "<h1>NO HAY RESULTADOS. PUEDES AGREGAR TU TRACK DANDO CLICK EN AGREGAR NUEVO TRACK</H1>";
		}
	}
}

class Album{
	public function __construct($a,$b,$e,$f){
			$this->AlbumId=$a;
			$this->Title=$b;
			$this->Artist=$e;
			$this->updated_at=$f;
	}
	
	public static function all($limit = 50, $offset = 0, $OB = "AlbumId", $OD = "ASC"){
		return self::get(null, null, $limit, $offset, $OB, $OD);
	}
	
	public static function find($id){
		return self::get($id, null, 1, 0, "AlbumId","ASC")[0];
	}
	
	public static function like($like, $limit = 50, $offset = 0, $OB = "AlbumId", $OD = "ASC"){
		return self::get(null, $like, $limit, $offset, $OB, $OD);
	}
	
	public static function create($title,$artist){
		return new self(null,$title,(is_int($artist)? $artist : $artist->ArtistId),null);
	}
	
	public function save(){
		if(!is_null($this->AlbumId)){
			$sQuery = 	"UPDATE album SET Title='$this->Title', updated_at=NOW(), ArtistId=".
						(is_int($this->Artist)? $this->Artist : $this->Artist->ArtistId)." ".
						"WHERE album.AlbumId = $this->AlbumId";
			return Conexion::getConexion()->query($sQuery);
		}else{
			$sQuery = "INSERT INTO album VALUES(null, '$this->Title', $this->Artist, NOW())";
			if(Conexion::getConexion()->query($sQuery)){
				$new_object = self::find(Conexion::getConexion()->insert_id);
				$this->AlbumId = $new_object->AlbumId;
				$this->Artist = $new_object->Artist;
				$this->updated_at = $new_object->updated_at;
				return $this;	
			}else{
				return false;
			}
		}
	}
	
	public function delete(){
		$sQuery = "DELETE FROM album WHERE album.AlbumId = $this->AlbumId";
		return Conexion::getConexion()->query($sQuery);
	}
	
	private static function get($id, $like, $limit, $offset, $OB, $OD){
		$sQuery = 	"SELECT ".
					"album.AlbumId AS 'album.AlbumId', ".
					"album.Title AS 'album.Title', ".
					"album.ArtistId AS 'album.ArtistId', ".
					"album.updated_at AS 'album.updated_at', ".
					"artist.ArtistId As 'artist.ArtistId', ".
					"artist.Name As 'artist.Name', ".
					"artist.updated_at As 'artist.updated_at' ".
					"FROM album INNER JOIN artist ON album.ArtistId = artist.ArtistId ".
					(!is_null($id)? "WHERE album.AlbumId = $id " : "").
					(!is_null($like)? "WHERE album.Title like '%$like%' " : "").
					"ORDER BY $OB $OD LIMIT $limit OFFSET $offset";				
		$query = Conexion::getConexion()->query($sQuery);
		while($album = $query->fetch_assoc())
			$albums[] = new self($album['album.AlbumId'],$album['album.Title'], 
							new Artist($album['artist.ArtistId'],$album['artist.Name'],$album['artist.updated_at']),
						$album['album.updated_at']);
		return $albums;
		echo $sQuery;
	}
}

class Artist{
	public function __construct($a,$b,$c){
			$this->ArtistId=$a;
			$this->Name=$b;
			$this->updated_at=$c;
	}
	
	public static function all($limit = 50, $offset = 0, $OB = "ArtistId", $OD = "ASC"){
		return self::get(null, null, $limit, $offset, $OB, $OD);
	}
	
	public static function find($id){
		return self::get($id, null, 1, 0, "ArtistId", "ASC")[0];
	}
	
	public static function like($like, $limit = 50, $offset = 0, $OB = "ArtistId", $OD = "ASC"){
		return self::get(null, $like, $limit, $offset, $OB, $OD);
	}
	
	public static function create($name){
		return new self(null,$name,null);
	}
		
	public function save(){
		if(!is_null($this->ArtistId)){
			$sQuery = "UPDATE artist SET Name='$this->Name', updated_at=NOW() WHERE ArtistId=$this->ArtistId";
			return Conexion::getConexion()->query($sQuery);
		}else{
			$sQuery = "INSERT INTO artist VALUES(null, '$this->Name', NOW())";
			if(Conexion::getConexion()->query($sQuery)){
				$this->ArtistId = Conexion::getConexion()->insert_id;
				return $this;	
			}else{
				return false;
			}
		}
	}
	
	public function delete(){
		$sQuery = "DELETE FROM artist WHERE artist.ArtistId = $this->ArtistId";
		return Conexion::getConexion()->query($sQuery);
	}
	
	private static function get($id, $like, $limit, $offset, $OB, $OD){

		$sQuery = 	"SELECT ".
					"artist.ArtistId As 'artist.ArtistId', ".
					"artist.Name As 'artist.Name', ".
					"artist.updated_at As 'artist.updated_at' ".
					"FROM artist ".
					(!is_null($id)? "WHERE artist.ArtistId = $id " : "").
					(!is_null($like)? "WHERE artist.Name like '%$like%' " : "").
					"ORDER BY $OB $OD LIMIT $limit OFFSET $offset";				
		$query = Conexion::getConexion()->query($sQuery);

		while($artist = $query->fetch_assoc())
			$artists[] = new self($artist['artist.ArtistId'],$artist['artist.Name'],$artist['artist.updated_at']);
		return $artists;
	}
	
}
?>