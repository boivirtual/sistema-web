<?php

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';

$DB_SRC = '`base_origem`';
$DB_DST = '`06692319676`';

// MYSQL Connect

$mysqli = new mysqli( $DB_HOST, $DB_USER, $DB_PASS ) or die( $mysqli->error );

// Create destination database

$mysqli->query( "CREATE DATABASE $DB_DST" ) or die( $mysqli->error . ' </br> Erro ao criar o banco' );

// Iterate through tables of source database

$mysqli->query("USE $DB_DST") or die( $mysqli->error . ' </br> Nenhum banco selecionado' );

$tables = $mysqli->query( "SHOW TABLES FROM $DB_SRC" ) or die( $mysqli->error . ' </br> Erro ao acessar o banco');

while( $table = $tables->fetch_array() ): $TABLE = $table[0];
    // Copy table and contents in destination database

    $mysqli->query( "CREATE TABLE $DB_DST.$TABLE LIKE $DB_SRC.$TABLE" ) or die( $mysqli->error );
    $mysqli->query( "INSERT INTO $DB_DST.$TABLE SELECT * FROM $DB_SRC.$TABLE" ) or die( $mysqli->error );
endwhile; 

echo "banco criado com sucesso!";
?>