<?php

namespace App\Model;

enum GenreEnum: string {
    case POP = 'Pop';
    case ROCK = 'Rock';
    case EDM = 'EDM';
    case RAP = 'Rap';
    case JAZZ = 'Jazz';
    case CLASSIC = 'Classique';
    case OTHER = 'Autre';
}