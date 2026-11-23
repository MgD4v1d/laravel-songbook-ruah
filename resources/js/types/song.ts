export type BlockType = 'verse' | 'chorus' | 'bridge';


export interface LyricsBlock {
    id: number;
    type: BlockType;
    label: string;
    content: string
}