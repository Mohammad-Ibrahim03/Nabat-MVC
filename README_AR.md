# Nabta Botanical — منصة التجارة الإلكترونية

هذا المشروع يجمع واجهة Nabta Botanical مع هيكل مجلد الفريق `e-commerce-platform`. المشروع مبني باستخدام **PHP + MySQL + PDO** وملفات Controllers / Models / Views، ولا يحتاج إلى Laravel أو Composer.

## التشغيل على XAMPP

1. انسخ مجلد `e-commerce-platform` إلى `C:\xampp\htdocs\`.
2. شغّل Apache وMySQL.
3. افتح phpMyAdmin واستورد `database/nabta_store2.sql`. الملف ينشئ قاعدة البيانات `nabta_store` والجداول والبيانات التجريبية.
4. انسخ `.env.example` إلى ملف جديد اسمه `.env` داخل جذر المشروع، ثم عدّل بيانات الاتصال عند الحاجة.
5. افتح `http://localhost/e-commerce-platform/`. سيتم تحويلك تلقائيًا إلى مجلد `public`.

**تنبيه:** ملف SQL يحتوي على `DROP TABLE IF EXISTS`، أي أن استيراده من جديد يحذف الجداول الموجودة التي تحمل الأسماء نفسها ثم يعيد إنشاءها. خذ نسخة احتياطية قبل استيراده على قاعدة بيانات مستخدمة. إذا كنت تستخدم قاعدة بيانات قديمة من نسخة Nabta السابقة، شغّل `database/google_signin_migration_existing_database.sql` مرة واحدة؛ سيضيف عمود Google عند الحاجة ويصلح كلمات مرور الحسابات التجريبية القديمة فقط عندما لا تزال قيمها النصية البديلة موجودة، ولن يغيّر كلمات مرور تم تحديثها.

## حسابات التجربة

| الدور | البريد | كلمة المرور |
|---|---|---|
| Admin | `mohammad@example.com` | `Admin@12345` |
| Customer | `sara@example.com` | `NabtaDemo#2026` |

هذه الحسابات مخصصة للتجربة المحلية فقط. غيّر كلمات المرور أو احذف الحسابات قبل النشر الفعلي. ويمكن للمستخدم إنشاء حساب جديد من صفحة Register.

## الوظائف الموجودة حاليًا

- الصفحة الرئيسية وصفحة About.
- عرض المنتجات والبحث والتصفية حسب Category والترتيب حسب السعر أو الشعبية.
- صفحة تفاصيل المنتج وحالة المخزون.
- Basket محفوظ داخل Session للزائر والمستخدم المسجّل.
- Checkout باستخدام Cash on Delivery مع تحديث المخزون داخل Database Transaction.
- عرض الطلبات السابقة وتحديث بيانات Profile وWishlist.
- Contact Us للمستخدم المسجّل، مع حفظ الرسائل في جدول `contact_us`.
- Plant Finder باقتراحات مبنية على قواعد بسيطة؛ ليست خدمة AI متصلة.
- Admin Dashboard لعرض الإحصائيات والطلبات وتحديث حالة الطلب.

صفحة البائعين / My Listings توضيحية للمرحلة القادمة؛ لأن Schema الحالي لا يحتوي على علاقة ملكية Seller داخل جدول `product` أو صلاحيات مراجعة الإعلانات. كذلك لا يوجد ربط ببوابة دفع إلكتروني أو تشخيص نباتات بالذكاء الاصطناعي في هذه النسخة.

## حل خطأ Internal Server Error (500)

تم تبسيط ملفات `.htaccess` لتجنب أوامر `Options` وإعادة كتابة الروابط التي قد لا يسمح بها إعداد Apache. الروابط تعمل مباشرة عبر `index.php?route=...` ولا تحتاج إلى `mod_rewrite`. إذا استمر الخطأ، أعد تسمية ملف `.htaccess` الموجود في جذر المشروع مؤقتًا إلى `.htaccess.disabled` ثم افتح `http://localhost/e-commerce-platform/` مجددًا. إذا اختفى الخطأ، راجع إعداد `AllowOverride` وسجل أخطاء Apache. لا تترك ملف `.env` مكشوفًا عند النشر الفعلي؛ اجعل `public/` هو `DocumentRoot`.

## Google Sign-In

كود Google Identity Services مربوط بصفحات Login وRegister. لتفعيله أنشئ OAuth Client ID من نوع Web application في Google Cloud Console، وأضف JavaScript origins الخاصة بـ localhost والدومين الفعلي، ثم ضع قيمة `GOOGLE_CLIENT_ID` في `.env`. لا تضع Google Client Secret داخل JavaScript. إذا لم تضف Client ID سيبقى زر Google مخفيًا، ويظل تسجيل الدخول العادي متاحًا.

## هيكل الملفات

```text
e-commerce-platform/
├── app/Controllers/       # منطق العمليات
├── app/Core/              # Database وSecurity وHelpers
├── app/Models/            # User وProduct وContactMessage
├── app/Views/             # صفحات PHP
├── config/                # إعدادات Database وGoogle وCloudinary
├── database/              # SQL Schema وSeed Data وMigration
├── docs/                  # التوثيق
└── public/                # نقطة الدخول والـ Assets العامة
```

جميع الصفحات تعمل من خلال `public/index.php` باستخدام `route`، مثل. لا يحتاج المشروع إلى `mod_rewrite` أو روابط مختصرة؛ الروابط تستخدم `index.php?route=...`:
`index.php?route=login` و`index.php?route=products` و`index.php?route=cart`.

## ملاحظات أمنية مهمة

- ملف `.env` لا يجب رفعه إلى GitHub؛ المسموح رفعه هو `.env.example` فقط.
- الملف المضغوط الذي وصلني كان يحتوي على Cloudinary API Secret. تمت إزالة القيمة من النسخة المسلّمة، لكن يجب **إلغاء المفتاح القديم وإنشاء Secret جديد** من لوحة Cloudinary، ثم حفظه محليًا داخل `.env`. حذف المفتاح من الملف لا يلغي صلاحيته إذا كان قد تم كشفه سابقًا.
- استخدم HTTPS عند النشر الفعلي، واضبط بيانات قاعدة البيانات وGoogle OAuth حسب بيئة الإنتاج.
- الصور الخاصة بالمنتجات في SQL تجريبية، ويستخدم الموديل صورًا عامة من `public/assets/img` إلى حين رفع صور مستقلة لكل منتج.

## حل المشاكل

- ظهور `could not find driver`: فعّل `pdo_mysql` في إعدادات PHP التي يستخدمها Apache ثم أعد تشغيل Apache.
- ظهور صفحة Database setup: تأكد من تشغيل MySQL وصحة ملف `.env` واسم قاعدة البيانات `nabta_store`.
- عدم ظهور Google Button: أضف `GOOGLE_CLIENT_ID` صالحًا إلى `.env` ثم أعد تحميل الصفحة.
- لا تفتح صفحات PHP باستخدام `file://`؛ استخدم XAMPP أو خادم PHP، أو اجعل مجلد `public` هو Document Root.
