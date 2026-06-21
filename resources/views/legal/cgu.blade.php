@extends('legal._layout')

@section('title', 'Conditions Générales d\'Utilisation')
@section('doc_title', 'Conditions Générales d\'Utilisation (CGU)')
@section('doc_version', '1.0')
@section('doc_date', '21/06/2026')

@section('doc_content')

<div class="alert alert-light border">
    Les présentes Conditions Générales d'Utilisation (CGU) régissent l'accès et l'utilisation
    de la plateforme « ADECOB Infrastructure Plannification » (ci-après « la Plateforme »),
    éditée par l'<strong>Association pour le Développement des Communes du Borgou (ADECOB)</strong>.
    Toute utilisation de la Plateforme implique l'acceptation pleine et entière des présentes CGU.
</div>

<h2>1. Objet</h2>
<p>La Plateforme a pour objet de permettre :</p>
<ul>
    <li>aux <strong>agents communaux</strong> de saisir et planifier les infrastructures
        (eau, assainissement, équipements publics) de leur commune ;</li>
    <li>aux <strong>administrateurs communaux</strong> de superviser ces données ;</li>
    <li>au <strong>public</strong> de consulter en lecture seule les infrastructures publiées
        à l'échelle des communes du Borgou.</li>
</ul>

<h2>2. Accès à la Plateforme</h2>
<p>L'accès aux espaces publics est libre et gratuit. L'accès aux espaces authentifiés
nécessite la création d'un compte, validée manuellement par un administrateur de l'ADECOB.
L'ADECOB se réserve le droit de refuser ou suspendre tout compte sans préavis en cas
d'usage non conforme aux présentes CGU.</p>

<h2>3. Compte utilisateur</h2>
<p>L'utilisateur s'engage à :</p>
<ul>
    <li>fournir des informations exactes et à jour lors de l'inscription ;</li>
    <li>conserver la confidentialité de son mot de passe et de son code MFA ;</li>
    <li>ne pas partager ses identifiants ;</li>
    <li>signaler sans délai toute compromission ou usage frauduleux de son compte.</li>
</ul>
<p>Toute action effectuée depuis un compte est <strong>présumée réalisée par son titulaire</strong>.</p>

<h2>4. Obligations de l'utilisateur</h2>
<p>L'utilisateur s'interdit notamment de :</p>
<ul>
    <li>tenter d'accéder à des espaces ou données qui ne lui sont pas destinés ;</li>
    <li>introduire ou tenter d'introduire du code malveillant ;</li>
    <li>effectuer des tests d'intrusion sans autorisation écrite préalable ;</li>
    <li>extraire massivement les données (<em>scraping</em>) en dehors des usages prévus ;</li>
    <li>publier des contenus illicites, diffamatoires ou contraires à l'ordre public ;</li>
    <li>saisir des données fictives ou volontairement erronées.</li>
</ul>
<p>Conformément aux articles 549 et suivants du Code du numérique du Bénin,
toute atteinte au système ou aux données est passible de sanctions pénales.</p>

<h2>5. Propriété intellectuelle</h2>
<p>L'ensemble des éléments composant la Plateforme (code, charte graphique, logo, textes)
est la propriété exclusive de l'ADECOB ou de ses partenaires. Toute reproduction,
représentation ou diffusion, totale ou partielle, sans autorisation écrite préalable
est interdite. Les <strong>données métier saisies</strong> (infrastructures, planifications)
restent la propriété des communes concernées ; l'ADECOB en assure l'hébergement et la valorisation
dans l'intérêt général.</p>

<h2>6. Disponibilité du service</h2>
<p>L'ADECOB met en œuvre les moyens raisonnables pour assurer l'accès continu à la Plateforme.
L'ADECOB peut toutefois interrompre temporairement le service pour maintenance, mise à jour,
ou en cas de force majeure, sans que cela puisse engager sa responsabilité.</p>

<h2>7. Données personnelles</h2>
<p>Le traitement des données personnelles est décrit dans la
<a href="{{ route('legal.confidentialite') }}">Politique de confidentialité</a>.</p>

<h2>8. Sécurité</h2>
<p>Les mesures techniques et organisationnelles de sécurité sont décrites dans la
<a href="{{ route('legal.pssi') }}">PSSI</a>. Tout utilisateur identifiant une vulnérabilité
est invité à la signaler de manière responsable via le
<a href="{{ route('contact') }}">formulaire de contact</a>.</p>

<h2>9. Responsabilité</h2>
<p>L'ADECOB ne saurait être tenue responsable :</p>
<ul>
    <li>des dommages indirects résultant de l'utilisation de la Plateforme ;</li>
    <li>des conséquences d'une utilisation non conforme aux présentes CGU ;</li>
    <li>de l'indisponibilité résultant du réseau Internet ou de l'équipement de l'utilisateur.</li>
</ul>

<h2>10. Modification des CGU</h2>
<p>Les présentes CGU peuvent être modifiées à tout moment. La version applicable est
celle en vigueur à la date de connexion. En cas de modification substantielle, l'utilisateur
en est informé lors de sa prochaine connexion.</p>

<h2>11. Loi applicable et juridiction compétente</h2>
<p>Les présentes CGU sont régies par le <strong>droit de la République du Bénin</strong>.
Tout litige relatif à leur interprétation ou exécution relève de la compétence
exclusive des juridictions béninoises.</p>

@endsection
