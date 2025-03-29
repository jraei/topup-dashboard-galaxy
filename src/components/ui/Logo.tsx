
import React from 'react';
import { cn } from '@/lib/utils';

interface LogoProps {
  className?: string;
}

const Logo: React.FC<LogoProps> = ({ className }) => {
  return (
    <div className={cn("font-bold text-xl flex items-center gap-2", className)}>
      <div className="h-8 w-8 rounded-md bg-gradient flex items-center justify-center text-white">
        N
      </div>
      <span className="text-gradient">Nexus</span>
    </div>
  );
};

export default Logo;
