import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import '../data.dart';
import '../models.dart';
import '../widgets/product_card.dart';

class HomePage extends StatelessWidget {
  const HomePage({super.key});

  List<Product> get featuredProducts => products.take(3).toList();

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final isWide = MediaQuery.of(context).size.width >= 900;

    return Scaffold(
      appBar: AppBar(
        title: _BrandTitle(theme: theme),
        actions: [
          TextButton(
            onPressed: () => Navigator.pushNamed(context, '/products'),
            child: const Text('Produtos'),
          ),
          TextButton(
            onPressed: () => Navigator.pushNamed(context, '/cart'),
            child: const Text('Carrinho'),
          ),
          const SizedBox(width: 16),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            _HeroSection(isWide: isWide),
            const SizedBox(height: 48),
            _SectionHeader(
              title: 'Nossos Destaques',
              subtitle: 'Conheça alguns dos nossos produtos mais queridos pelos clientes',
            ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 24),
              child: SizedBox(
                height: 440,
                child: GridView.builder(
                  itemCount: featuredProducts.length,
                  scrollDirection: Axis.horizontal,
                  primary: false,
                  shrinkWrap: true,
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 1,
                    childAspectRatio: 0.8,
                    mainAxisExtent: 340,
                  ),
                  itemBuilder: (context, index) => ProductCard(product: featuredProducts[index]),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: OutlinedButton(
                onPressed: () => Navigator.pushNamed(context, '/products'),
                child: const Text('Ver todos os produtos'),
              ),
            ),
            const SizedBox(height: 64),
            const _AboutSection(),
            const SizedBox(height: 64),
            const _TestimonialsSection(),
            const SizedBox(height: 64),
          ],
        ),
      ),
    );
  }
}

class _HeroSection extends StatelessWidget {
  const _HeroSection({required this.isWide});

  final bool isWide;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: isWide ? 96 : 24,
        vertical: isWide ? 120 : 72,
      ),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            theme.colorScheme.primary.withOpacity(0.15),
            theme.colorScheme.secondary.withOpacity(0.08),
          ],
        ),
      ),
      child: Flex(
        direction: isWide ? Axis.horizontal : Axis.vertical,
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  'Doces Artesanais',
                  style: theme.textTheme.displaySmall?.copyWith(fontWeight: FontWeight.bold),
                ),
                Text(
                  'Feitos com Amor',
                  style: GoogleFonts.cookie(
                    color: theme.colorScheme.primary,
                    fontSize: 64,
                  ),
                ),
                const SizedBox(height: 16),
                Text(
                  'Transforme seus momentos especiais em memórias deliciosas com nossos doces artesanais.',
                  style: theme.textTheme.titleMedium?.copyWith(
                    color: theme.colorScheme.onSurfaceVariant,
                  ),
                ),
                const SizedBox(height: 24),
                Wrap(
                  spacing: 16,
                  runSpacing: 16,
                  children: [
                    ElevatedButton(
                      onPressed: () => Navigator.pushNamed(context, '/products'),
                      child: const Text('Ver Produtos'),
                    ),
                    OutlinedButton(
                      onPressed: () => Navigator.pushNamed(context, '/cart'),
                      child: const Text('Carrinho'),
                    ),
                  ],
                ),
              ],
            ),
          ),
          if (isWide) const SizedBox(width: 48) else const SizedBox(height: 32),
          Flexible(
            child: ClipRRect(
              borderRadius: BorderRadius.circular(32),
              child: AspectRatio(
                aspectRatio: 4 / 3,
                child: Image.asset(
                  'assets/hero-sweets.jpg',
                  fit: BoxFit.cover,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _SectionHeader extends StatelessWidget {
  const _SectionHeader({required this.title, required this.subtitle});

  final String title;
  final String subtitle;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: theme.textTheme.headlineMedium,
          ),
          const SizedBox(height: 12),
          Text(
            subtitle,
            style: theme.textTheme.bodyLarge?.copyWith(
              color: theme.colorScheme.onSurfaceVariant,
            ),
          ),
        ],
      ),
    );
  }
}

class _AboutSection extends StatelessWidget {
  const _AboutSection();

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final isWide = MediaQuery.of(context).size.width > 900;

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Flex(
        direction: isWide ? Axis.horizontal : Axis.vertical,
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Sobre a Doce Encanto',
                  style: theme.textTheme.headlineMedium,
                ),
                Text(
                  'Doce Encanto',
                  style: GoogleFonts.cookie(
                    textStyle: theme.textTheme.displaySmall?.copyWith(
                      color: theme.colorScheme.primary,
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                Text(
                  'Há mais de 10 anos criando doces artesanais que encantam e conquistam corações. Nossa missão é transformar ingredientes selecionados em verdadeiras obras de arte comestíveis.',
                  style: theme.textTheme.bodyLarge?.copyWith(color: theme.colorScheme.onSurfaceVariant),
                ),
                const SizedBox(height: 12),
                Text(
                  'Cada produto é feito com carinho, atenção aos detalhes e os melhores ingredientes, garantindo sabor incomparável e apresentação impecável.',
                  style: theme.textTheme.bodyLarge?.copyWith(color: theme.colorScheme.onSurfaceVariant),
                ),
                const SizedBox(height: 24),
                Wrap(
                  spacing: 32,
                  runSpacing: 24,
                  children: const [
                    _Highlight(icon: Icons.favorite, label: 'Feito com Amor'),
                    _Highlight(icon: Icons.star, label: 'Ingredientes Premium'),
                    _Highlight(icon: Icons.verified, label: 'Qualidade Garantida'),
                  ],
                ),
              ],
            ),
          ),
          if (isWide) const SizedBox(width: 48) else const SizedBox(height: 32),
          Flexible(
            child: ClipRRect(
              borderRadius: BorderRadius.circular(32),
              child: AspectRatio(
                aspectRatio: 3 / 4,
                child: Image.asset(
                  'assets/hero-sweets.jpg',
                  fit: BoxFit.cover,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _TestimonialsSection extends StatelessWidget {
  const _TestimonialsSection();

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'O que dizem nossos clientes',
            style: theme.textTheme.headlineMedium,
          ),
          const SizedBox(height: 24),
          LayoutBuilder(
            builder: (context, constraints) {
              final crossAxisCount = constraints.maxWidth > 1000
                  ? 3
                  : constraints.maxWidth > 700
                      ? 2
                      : 1;

              return GridView.builder(
                itemCount: testimonials.length,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: crossAxisCount,
                  mainAxisSpacing: 24,
                  crossAxisSpacing: 24,
                  childAspectRatio: crossAxisCount == 1 ? 1.1 : 0.9,
                ),
                itemBuilder: (context, index) => _TestimonialCard(testimonial: testimonials[index]),
              );
            },
          ),
        ],
      ),
    );
  }
}

class _TestimonialCard extends StatelessWidget {
  const _TestimonialCard({required this.testimonial});

  final Testimonial testimonial;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        gradient: LinearGradient(
          colors: [theme.colorScheme.surface, theme.colorScheme.surfaceVariant],
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
        ),
        boxShadow: [
          BoxShadow(
            color: theme.shadowColor.withOpacity(0.08),
            blurRadius: 24,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: List.generate(
              testimonial.rating,
              (index) => const Icon(Icons.star, color: Colors.amber, size: 20),
            ),
          ),
          const SizedBox(height: 12),
          Text(
            '“${testimonial.text}”',
            style: theme.textTheme.bodyLarge?.copyWith(
              fontStyle: FontStyle.italic,
              color: theme.colorScheme.onSurfaceVariant,
            ),
          ),
          const SizedBox(height: 16),
          Text(
            '— ${testimonial.name}',
            style: theme.textTheme.titleMedium?.copyWith(color: theme.colorScheme.primary),
          ),
        ],
      ),
    );
  }
}

class _Highlight extends StatelessWidget {
  const _Highlight({required this.icon, required this.label});

  final IconData icon;
  final String label;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, color: theme.colorScheme.primary, size: 32),
        const SizedBox(height: 12),
        Text(
          label,
          style: theme.textTheme.titleMedium,
        ),
      ],
    );
  }
}

class _BrandTitle extends StatelessWidget {
  const _BrandTitle({required this.theme});

  final ThemeData theme;

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Text(
          'Doce',
          style: theme.textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
        ),
        const SizedBox(width: 4),
        Text(
          'Encanto',
          style: GoogleFonts.cookie(
            textStyle: theme.textTheme.headlineSmall?.copyWith(
              color: theme.colorScheme.primary,
            ),
          ),
        ),
      ],
    );
  }
}
