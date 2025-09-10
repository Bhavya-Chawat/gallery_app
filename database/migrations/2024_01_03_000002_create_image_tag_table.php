    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('image_tag', function (Blueprint $table) {
                $table->id();   
                $table->foreignUuid('image_id')->constrained()->onDelete('cascade');
                $table->foreignId('tag_id')->constrained()->onDelete('cascade');
                $table->unique(['image_id', 'tag_id']);
                $table->index('tag_id');
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('image_tag');
        }
    };
