
import React from 'react';
import { Star } from 'lucide-react';

interface TestimonialProps {
  quote: string;
  author: string;
  role: string;
  company: string;
  rating: number;
  delay: string;
}

const Testimonial: React.FC<TestimonialProps> = ({ quote, author, role, company, rating, delay }) => {
  return (
    <div 
      className="bg-white p-6 rounded-xl shadow-sm border border-border/50 flex flex-col h-full animate-fade-in"
      style={{ animationDelay: delay }}
    >
      <div className="flex mb-4">
        {Array.from({ length: 5 }).map((_, i) => (
          <Star
            key={i}
            className={`h-5 w-5 ${i < rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'}`}
          />
        ))}
      </div>
      <blockquote className="flex-grow">
        <p className="text-foreground leading-relaxed mb-4">{quote}</p>
      </blockquote>
      <div className="mt-auto">
        <p className="font-semibold">{author}</p>
        <p className="text-sm text-muted-foreground">
          {role}, {company}
        </p>
      </div>
    </div>
  );
};

const Testimonials: React.FC = () => {
  const testimonials = [
    {
      quote: "Working with Nexus transformed our online presence. The website is not only beautiful but also performs exceptionally well, leading to increased conversion rates.",
      author: "Sarah Johnson",
      role: "Marketing Director",
      company: "TechCorp",
      rating: 5,
      delay: "0s"
    },
    {
      quote: "The attention to detail and commitment to quality is what sets Nexus apart. Our new website has received countless compliments and has significantly improved user engagement.",
      author: "Michael Chen",
      role: "CEO",
      company: "Innovate Labs",
      rating: 5,
      delay: "0.1s"
    },
    {
      quote: "I was blown away by how quickly Nexus delivered a stunning website that perfectly captured our brand. The responsive design works flawlessly across all devices.",
      author: "Emma Rodriguez",
      role: "Creative Director",
      company: "Design Studio",
      rating: 4,
      delay: "0.2s"
    }
  ];

  return (
    <section id="testimonials" className="py-20 lg:py-32 bg-secondary/50">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold mb-4 animate-fade-in">
            Trusted by <span className="text-gradient">Industry Leaders</span>
          </h2>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto animate-fade-in" style={{ animationDelay: "0.1s" }}>
            Hear what our clients have to say about their experience working with us.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {testimonials.map((testimonial, index) => (
            <Testimonial
              key={index}
              quote={testimonial.quote}
              author={testimonial.author}
              role={testimonial.role}
              company={testimonial.company}
              rating={testimonial.rating}
              delay={testimonial.delay}
            />
          ))}
        </div>
      </div>
    </section>
  );
};

export default Testimonials;
