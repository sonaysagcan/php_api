Challange seçeneklerinden Api + Worker tercih edilmiştir.

WORKER:
Worker bir laravel custom command olarak oluşturulmuştur. (App/Console/Commands/ExpiredSubscriptionWorker)
Worker çalışırken windows yüklü i7 makinamda yaklaşık 200 - 300 mb RAM ve %15-%19 arası cpu kaynağı kullanmaktadır.
php artisan check:subscription komutu ile çalıştırılmaktadır.
Standart ayarlarda memory limit hatasından kaçınmak için aşağıdaki şekilde çalıştırıyorum. 
php -d memory_limit=1000M artisan check:subscription

DB:
Veritabanında birkaç yüzbin civarında dummy kayıt oluşturulmuştur.
İstendiğinde denenmesi için açıklamalarda dikkat çektiğiniz 10 milyon üzerine çıkartabilirim.
