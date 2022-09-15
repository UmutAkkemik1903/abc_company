# abc_company

ABC Company tedarikçi bir firmadır ve 3 müşterisi vardır. Bu müşterilerin kendine ait
kullanıcı adları ve şifreleri vardır. Müşteriler Restful servislerini kullanarak sipariş oluşturabilirler ve görebilirler. Bu proje Symfony 5.4.12 ile oluşturulmuştur.

# Kurulum
.env dosyasında DATABASE_URL ayarlamasını yapın.
"config" klasörünün altında "jwt" adında klasör oluşturun.
## Terminal ekranında;
<br/>
cd symfony -- Klasör dizinine gider.
<br/>
composer install  -- Vendor dosyasını kurar.
<br/>
php bin/console doctrine:database:create  -- Veri tabanını ekler.
<br/>
php bin/console make:migration -- Projede bulunan entity dosyalarını migration haline getirir.
<br/>
php bin/console doctrine:migrations:migrate -- Oluşturulan migrationu veri tabanına ekler
<br/>
php bin/console doctrine:fixtures:load -- Oluşturulan Fixturesleri veri tabanında ilgili tablolara ekler.
<br/>
openssl genrsa -out config/jwt/private.pem -aes256 4096 -- pass phrase için .env klasöründe "JWT_PASSPHRASE" alanındaki değeri yazın. Değeri tekrar yazarak onaylayın.
<br/>
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem -- pass phrase için .env klasöründe "JWT_PASSPHRASE" alanındaki değeri yazın.
<br/>
php -S 127.0.0.1:8080 -t public -- Serverı ayağa kaldırır.
